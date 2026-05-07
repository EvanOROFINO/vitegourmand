<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Models\Commande;
use App\Models\Menu;
use App\Models\Stats;
use App\Models\User;

final class CommandeController extends Controller
{
    public function showForm(string $menu_id): void
    {
        Auth::requireLogin();
        $menu = Menu::find((int) $menu_id);
        if (!$menu || !$menu['actif']) {
            $this->flash('error', 'Menu introuvable.');
            $this->redirect('/menus');
        }

        $user = User::findById((int) Auth::id());

        $this->view('commande/form', [
            'pageTitle' => 'Commander : ' . $menu['titre'],
            'menu'      => $menu,
            'user'      => $user,
        ]);
    }

    /**
     * Endpoint JSON pour calcul prix dynamique côté client.
     */
    public function apiCalculPrix(): void
    {
        Auth::requireLogin();
        $menu = Menu::find((int) ($_POST['menu_id'] ?? 0));
        if (!$menu) {
            $this->json(['error' => 'Menu introuvable'], 404);
        }

        $nbPersonnes = (int) ($_POST['nombre_personne'] ?? 0);
        $ville       = trim((string) ($_POST['ville_livraison'] ?? ''));
        $distance    = (float) ($_POST['distance_km'] ?? 0);

        if ($nbPersonnes < $menu['nombre_personne_minimum']) {
            $this->json(['error' => 'Nombre de personnes inférieur au minimum requis (' . $menu['nombre_personne_minimum'] . ').'], 400);
        }

        $prix = Commande::calculerPrix($menu, $nbPersonnes, $ville, $distance);
        $this->json($prix);
    }

    public function submit(): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();

        $menu = Menu::find((int) ($_POST['menu_id'] ?? 0));
        if (!$menu || !$menu['actif']) {
            $this->flash('error', 'Menu introuvable.');
            $this->redirect('/menus');
        }

        $datePrestation = $this->input('date_prestation');
        $heureLivraison = $this->input('heure_livraison');
        $adresse        = $this->input('adresse_livraison');
        $ville          = $this->input('ville_livraison');
        $nbPersonnes    = (int) ($_POST['nombre_personne'] ?? 0);
        $distanceKm     = (float) ($_POST['distance_km'] ?? 0);

        if (empty($datePrestation) || empty($heureLivraison) || empty($adresse) || empty($ville)) {
            $this->flash('error', 'Tous les champs sont obligatoires.');
            $this->redirect('/commander/' . $menu['menu_id']);
        }
        if ($nbPersonnes < $menu['nombre_personne_minimum']) {
            $this->flash('error', 'Le nombre de personnes minimum est de ' . $menu['nombre_personne_minimum'] . '.');
            $this->redirect('/commander/' . $menu['menu_id']);
        }
        if ($menu['quantite_restante'] <= 0) {
            $this->flash('error', 'Désolé, ce menu n\'est plus disponible.');
            $this->redirect('/menus');
        }
        if (strtotime((string) $datePrestation) < strtotime('+1 day')) {
            $this->flash('error', 'La date de prestation doit être au moins le lendemain.');
            $this->redirect('/commander/' . $menu['menu_id']);
        }

        $prix = Commande::calculerPrix($menu, $nbPersonnes, (string) $ville, $distanceKm);

        $numero = numero_commande();
        Commande::create([
            'numero_commande'    => $numero,
            'utilisateur_id'     => Auth::id(),
            'menu_id'            => $menu['menu_id'],
            'date_prestation'    => $datePrestation,
            'heure_livraison'    => $heureLivraison,
            'adresse_livraison'  => $adresse,
            'ville_livraison'    => $ville,
            'distance_km'        => $distanceKm,
            'nombre_personne'    => $nbPersonnes,
            'prix_menu'          => $prix['prix_menu'],
            'prix_livraison'     => $prix['prix_livraison'],
            'reduction'          => $prix['reduction'],
            'prix_total'         => $prix['prix_total'],
        ]);

        // Statistiques NoSQL
        Stats::incrementCommandeMenu((int) $menu['menu_id'], $menu['titre'], $prix['prix_total']);
        Stats::log('commande_creee', ['numero' => $numero, 'user_id' => Auth::id()]);

        // Mail de confirmation
        $user = Auth::user();
        Mailer::send(
            (string) $user['email'],
            'Confirmation de votre commande ' . $numero,
            '<h1>Merci pour votre commande !</h1>
             <p>Bonjour ' . e($user['prenom']) . ',</p>
             <p>Votre commande <strong>' . e($numero) . '</strong> a bien été reçue.</p>
             <p><strong>Menu :</strong> ' . e($menu['titre']) . '<br>
                <strong>Date :</strong> ' . e((string) $datePrestation) . ' à ' . e((string) $heureLivraison) . '<br>
                <strong>Adresse :</strong> ' . e((string) $adresse) . ', ' . e((string) $ville) . '<br>
                <strong>Personnes :</strong> ' . $nbPersonnes . '<br>
                <strong>Total :</strong> ' . format_prix($prix['prix_total']) . '</p>
             <p>Notre équipe vous contactera pour valider votre commande.</p>'
        );

        $this->flash('success', 'Commande enregistrée ! Numéro : ' . $numero);
        $this->redirect('/mon-espace/commandes/' . urlencode($numero));
    }
}
