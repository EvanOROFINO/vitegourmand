<?php
/**
 * CommandeController — Couche HTTP : reçoit la requête, délègue au CommandeService.
 *
 * Aucun calcul métier, aucune requête SQL : ce controller orchestre uniquement
 * l'entrée HTTP, la validation CSRF/auth, et la sortie (redirect / JSON / vue).
 */
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Repositories\MenuRepository;
use App\Repositories\UserRepository;
use App\Services\CommandeService;
use DomainException;

final class CommandeController extends Controller
{
    public function __construct(private CommandeService $service = new CommandeService()) {}

    /**
     * Affiche le formulaire de commande pour un menu donné.
     */
    public function showForm(string $menu_id): void
    {
        Auth::requireLogin();
        $menu = MenuRepository::find((int) $menu_id);
        if (!$menu || !$menu['actif']) {
            $this->flash('error', 'Menu introuvable.');
            $this->redirect('/menus');
        }

        $user = UserRepository::findById((int) Auth::id());

        $this->view('commande/form', [
            'pageTitle' => 'Commander : ' . $menu['titre'],
            'menu'      => $menu,
            'user'      => $user,
        ]);
    }

    /**
     * Endpoint JSON pour le calcul de prix dynamique côté client (utilisé par fetch JS).
     */
    public function apiCalculPrix(): void
    {
        Auth::requireLogin();
        try {
            $prix = $this->service->previewPrix(
                (int)   ($_POST['menu_id'] ?? 0),
                (int)   ($_POST['nombre_personne'] ?? 0),
                trim((string) ($_POST['ville_livraison'] ?? '')),
                (float) ($_POST['distance_km'] ?? 0),
            );
            $this->json($prix);
        } catch (DomainException $e) {
            $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Soumission du formulaire de commande — délègue la création au service.
     */
    public function submit(): void
    {
        Auth::requireLogin();
        $this->verifyCsrf();

        try {
            $result = $this->service->creerCommande([
                'menu_id'            => $_POST['menu_id'] ?? 0,
                'date_prestation'    => $this->input('date_prestation'),
                'heure_livraison'    => $this->input('heure_livraison'),
                'adresse_livraison'  => $this->input('adresse_livraison'),
                'ville_livraison'    => $this->input('ville_livraison'),
                'nombre_personne'    => $_POST['nombre_personne'] ?? 0,
                'distance_km'        => $_POST['distance_km'] ?? 0,
            ], (int) Auth::id());
        } catch (DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/commander/' . (int) ($_POST['menu_id'] ?? 0));
        }

        // Envoi du mail de confirmation (effet de bord HTTP)
        $this->envoyerMailConfirmation($result, Auth::user());

        $this->flash('success', 'Commande enregistrée ! Numéro : ' . $result['numero']);
        $this->redirect('/mon-espace/commandes/' . urlencode($result['numero']));
    }

    /**
     * Envoie le mail de confirmation. Volontairement private au controller car
     * c'est un effet de bord HTTP (l'utilisateur connecté reçoit un mail).
     */
    private function envoyerMailConfirmation(array $result, ?array $user): void
    {
        if (!$user) return;

        $menu = $result['menu'];
        $prix = $result['prix'];
        $numero = $result['numero'];

        Mailer::send(
            (string) $user['email'],
            'Confirmation de votre commande ' . $numero,
            '<h1>Merci pour votre commande !</h1>
             <p>Bonjour ' . e($user['prenom']) . ',</p>
             <p>Votre commande <strong>' . e($numero) . '</strong> a bien été reçue.</p>
             <p><strong>Menu :</strong> ' . e($menu['titre']) . '<br>
                <strong>Total :</strong> ' . format_prix($prix['prix_total']) . '</p>
             <p>Notre équipe vous contactera pour valider votre commande.</p>'
        );
    }
}
