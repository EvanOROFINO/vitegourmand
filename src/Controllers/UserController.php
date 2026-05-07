<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Avis;
use App\Models\Commande;
use App\Models\Menu;
use App\Models\User;

final class UserController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireLogin();
        $commandes = Commande::findByUser((int) Auth::id());
        $this->view('user/dashboard', [
            'pageTitle'  => 'Mon espace',
            'commandes'  => $commandes,
            'nbCommandes'=> count($commandes),
        ]);
    }

    public function profil(): void
    {
        Auth::requireLogin();
        $user = User::findById((int) Auth::id());
        $this->view('user/profil', [
            'pageTitle' => 'Mon profil',
            'user'      => $user,
        ]);
    }

    public function updateProfil(): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();

        $data = [
            'prenom'          => $this->input('prenom'),
            'nom'             => $this->input('nom'),
            'telephone'       => $this->input('telephone'),
            'adresse_postale' => $this->input('adresse_postale'),
            'ville'           => $this->input('ville'),
            'pays'            => $this->input('pays') ?: 'France',
        ];

        User::update((int) Auth::id(), $data);
        $this->flash('success', 'Profil mis à jour.');
        $this->redirect('/mon-espace/profil');
    }

    public function commandes(): void
    {
        Auth::requireLogin();
        $this->view('user/commandes', [
            'pageTitle' => 'Mes commandes',
            'commandes' => Commande::findByUser((int) Auth::id()),
        ]);
    }

    public function commandeDetail(string $numero): void
    {
        Auth::requireLogin();
        $commande = Commande::find($numero);
        if (!$commande || (int) $commande['utilisateur_id'] !== Auth::id()) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }
        $this->view('user/commande-detail', [
            'pageTitle'  => 'Commande ' . $numero,
            'commande'   => $commande,
            'historique' => Commande::historique($numero),
            'avis'       => Avis::findByCommande($numero),
        ]);
    }

    public function annulerCommande(string $numero): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();
        $commande = Commande::find($numero);
        if (!$commande || (int) $commande['utilisateur_id'] !== Auth::id()) {
            $this->flash('error', 'Commande introuvable.');
            $this->redirect('/mon-espace/commandes');
        }
        if ($commande['statut'] !== 'en_attente') {
            $this->flash('error', 'Cette commande ne peut plus être annulée.');
            $this->redirect('/mon-espace/commandes/' . urlencode($numero));
        }

        $motif = $this->input('motif') ?: 'Annulation par le client';
        Commande::annuler($numero, (string) $motif, 'client');
        $this->flash('success', 'Commande annulée.');
        $this->redirect('/mon-espace/commandes');
    }

    public function modifierCommande(string $numero): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();
        $commande = Commande::find($numero);
        if (!$commande || (int) $commande['utilisateur_id'] !== Auth::id()) {
            $this->flash('error', 'Commande introuvable.');
            $this->redirect('/mon-espace/commandes');
        }
        if ($commande['statut'] !== 'en_attente') {
            $this->flash('error', 'Cette commande ne peut plus être modifiée.');
            $this->redirect('/mon-espace/commandes/' . urlencode($numero));
        }

        $menu = Menu::find((int) $commande['menu_id']);
        $nbPersonnes = max((int) ($_POST['nombre_personne'] ?? $commande['nombre_personne']), $menu['nombre_personne_minimum']);
        $ville       = $this->input('ville_livraison') ?: $commande['ville_livraison'];
        $distance    = (float) ($_POST['distance_km'] ?? $commande['distance_km']);
        $prix        = Commande::calculerPrix($menu, $nbPersonnes, (string) $ville, $distance);

        Commande::modifier($numero, [
            'date_prestation'   => $this->input('date_prestation') ?: $commande['date_prestation'],
            'heure_livraison'   => $this->input('heure_livraison') ?: $commande['heure_livraison'],
            'adresse_livraison' => $this->input('adresse_livraison') ?: $commande['adresse_livraison'],
            'ville_livraison'   => $ville,
            'distance_km'       => $distance,
            'nombre_personne'   => $nbPersonnes,
            'prix_menu'         => $prix['prix_menu'],
            'prix_livraison'    => $prix['prix_livraison'],
            'reduction'         => $prix['reduction'],
            'prix_total'        => $prix['prix_total'],
        ]);

        $this->flash('success', 'Commande modifiée.');
        $this->redirect('/mon-espace/commandes/' . urlencode($numero));
    }

    public function showAvisForm(string $numero): void
    {
        Auth::requireLogin();
        $commande = Commande::find($numero);
        if (!$commande || (int) $commande['utilisateur_id'] !== Auth::id()) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }
        if ($commande['statut'] !== 'terminee') {
            $this->flash('error', 'Vous pourrez laisser un avis une fois la commande terminée.');
            $this->redirect('/mon-espace/commandes/' . urlencode($numero));
        }
        if (Avis::findByCommande($numero)) {
            $this->flash('info', 'Vous avez déjà laissé un avis pour cette commande.');
            $this->redirect('/mon-espace/commandes/' . urlencode($numero));
        }
        $this->view('user/avis', [
            'pageTitle' => 'Donner un avis',
            'commande'  => $commande,
        ]);
    }

    public function submitAvis(string $numero): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();

        $commande = Commande::find($numero);
        if (!$commande || (int) $commande['utilisateur_id'] !== Auth::id() || $commande['statut'] !== 'terminee') {
            $this->flash('error', 'Action impossible.');
            $this->redirect('/mon-espace/commandes');
        }

        $note        = max(1, min(5, (int) ($_POST['note'] ?? 0)));
        $description = (string) $this->input('description');

        if (empty($description)) {
            $this->flash('error', 'Veuillez écrire un commentaire.');
            $this->redirect('/mon-espace/commandes/' . urlencode($numero) . '/avis');
        }

        Avis::create((int) Auth::id(), $numero, $note, $description);
        $this->flash('success', 'Merci pour votre avis ! Il sera publié après validation par un employé.');
        $this->redirect('/mon-espace/commandes/' . urlencode($numero));
    }
}
