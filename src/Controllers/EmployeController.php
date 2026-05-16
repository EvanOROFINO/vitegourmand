<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Repositories\AvisRepository;
use App\Repositories\CommandeRepository;
use App\Repositories\HoraireRepository;
use App\Repositories\MenuRepository;
use App\Repositories\PlatRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\ThemeRepository;
use App\Services\AvisService;
use App\Services\CommandeService;
use DomainException;

class EmployeController extends Controller
{
    private CommandeService $commandeService;
    private AvisService $avisService;

    public function __construct()
    {
        Auth::requireRole(['employe', 'administrateur']);
        $this->commandeService = new CommandeService();
        $this->avisService     = new AvisService();
    }

    public function dashboard(): void
    {
        $statut    = $_GET['statut'] ?? null;
        $recherche = $_GET['q']      ?? null;
        $commandes = CommandeRepository::listForStaff($statut, $recherche);

        $this->view('employe/dashboard', [
            'pageTitle'    => 'Espace employé',
            'commandes'    => $commandes,
            'statut'       => $statut,
            'recherche'    => $recherche,
            'nbAvisAttente'=> count(AvisRepository::enAttente()),
        ]);
    }

    // ----- Commandes -----
    public function commandes(): void { $this->dashboard(); }

    public function changerStatut(string $numero): void
    {
        $this->verifyCsrf();
        $statut = (string) $this->input('statut');

        // La logique métier (validation transition, log) est dans le Service
        try {
            $this->commandeService->changerStatut($numero, $statut, $this->input('commentaire'));
        } catch (DomainException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('/employe/commandes');
        }

        // Effets de bord HTTP : envoi de mails sur certains statuts
        $this->envoyerMailsTransition($numero, $statut);

        $this->flash('success', 'Statut mis à jour : ' . format_statut($statut));
        $this->redirect('/employe/commandes');
    }

    /**
     * Effet de bord HTTP : envoie les mails automatiques liés à certains statuts.
     */
    private function envoyerMailsTransition(string $numero, string $statut): void
    {
        $commande = CommandeRepository::find($numero);
        if (!$commande) return;

        if ($statut === 'en_attente_retour_materiel' && $commande['pret_materiel']) {
            Mailer::send(
                $commande['email'],
                'Restitution du matériel — commande ' . $numero,
                '<p>Bonjour ' . e($commande['prenom']) . ',</p>
                 <p>Votre commande est livrée. Du matériel vous a été prêté.</p>
                 <p><strong>Si le matériel n\'est pas restitué sous 10 jours ouvrés, vous devrez vous acquitter de 600 €</strong> (mentionné dans nos CGV).</p>
                 <p>Pour rendre le matériel, merci de nous contacter.</p>'
            );
        }
        if ($statut === 'terminee') {
            Mailer::send(
                $commande['email'],
                'Votre commande est terminée — laissez votre avis',
                '<p>Bonjour ' . e($commande['prenom']) . ',</p>
                 <p>Votre commande ' . e($numero) . ' est terminée. Connectez-vous à votre espace pour laisser un avis !</p>'
            );
        }
    }

    public function annulerCommande(string $numero): void
    {
        $this->verifyCsrf();
        $motif       = $this->input('motif');
        $modeContact = $this->input('mode_contact');

        if (empty($motif) || !in_array($modeContact, ['gsm', 'mail'], true)) {
            $this->flash('error', 'Motif et mode de contact obligatoires.');
            $this->redirect('/employe/commandes');
        }

        CommandeRepository::annuler($numero, (string) $motif, (string) $modeContact);
        $this->flash('success', 'Commande annulée.');
        $this->redirect('/employe/commandes');
    }

    // ----- Menus -----
    public function menus(): void
    {
        $this->view('employe/menus', [
            'pageTitle' => 'Gérer les menus',
            'menus'     => MenuRepository::search(),
        ]);
    }

    public function newMenu(): void
    {
        $this->view('employe/menu-form', [
            'pageTitle' => 'Nouveau menu',
            'menu'      => null,
            'themes'    => ThemeRepository::all(),
            'regimes'   => RegimeRepository::all(),
            'plats'     => PlatRepository::all(),
        ]);
    }

    public function createMenu(): void
    {
        $this->verifyCsrf();
        $menuId = MenuRepository::create([
            'titre'                   => $this->input('titre'),
            'description'             => $this->input('description'),
            'nombre_personne_minimum' => (int) ($_POST['nombre_personne_minimum'] ?? 0),
            'prix_par_personne'       => (float) ($_POST['prix_par_personne'] ?? 0),
            'conditions_menu'         => $this->input('conditions_menu'),
            'quantite_restante'       => (int) ($_POST['quantite_restante'] ?? 0),
            'theme_id'                => (int) ($_POST['theme_id'] ?? 0),
            'regime_id'               => (int) ($_POST['regime_id'] ?? 0),
            'actif'                   => !empty($_POST['actif']),
        ]);
        MenuRepository::setPlats($menuId, $_POST['plats'] ?? []);
        $this->flash('success', 'Menu créé.');
        $this->redirect('/employe/menus');
    }

    public function editMenu(string $id): void
    {
        $menu = MenuRepository::find((int) $id);
        if (!$menu) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }
        $this->view('employe/menu-form', [
            'pageTitle' => 'Modifier : ' . $menu['titre'],
            'menu'      => $menu,
            'themes'    => ThemeRepository::all(),
            'regimes'   => RegimeRepository::all(),
            'plats'     => PlatRepository::all(),
        ]);
    }

    public function updateMenu(string $id): void
    {
        $this->verifyCsrf();
        MenuRepository::update((int) $id, [
            'titre'                   => $this->input('titre'),
            'description'             => $this->input('description'),
            'nombre_personne_minimum' => (int) ($_POST['nombre_personne_minimum'] ?? 0),
            'prix_par_personne'       => (float) ($_POST['prix_par_personne'] ?? 0),
            'conditions_menu'         => $this->input('conditions_menu'),
            'quantite_restante'       => (int) ($_POST['quantite_restante'] ?? 0),
            'theme_id'                => (int) ($_POST['theme_id'] ?? 0),
            'regime_id'               => (int) ($_POST['regime_id'] ?? 0),
            'actif'                   => !empty($_POST['actif']),
        ]);
        MenuRepository::setPlats((int) $id, $_POST['plats'] ?? []);
        $this->flash('success', 'Menu mis à jour.');
        $this->redirect('/employe/menus');
    }

    public function deleteMenu(string $id): void
    {
        $this->verifyCsrf();
        MenuRepository::delete((int) $id);
        $this->flash('success', 'Menu désactivé.');
        $this->redirect('/employe/menus');
    }

    // ----- Plats -----
    public function plats(): void
    {
        $this->view('employe/plats', [
            'pageTitle' => 'Gérer les plats',
            'plats'     => PlatRepository::all(),
        ]);
    }

    public function createPlat(): void
    {
        $this->verifyCsrf();
        PlatRepository::create([
            'titre' => $this->input('titre'),
            'type'  => $this->input('type'),
            'photo' => null,
        ]);
        $this->flash('success', 'Plat ajouté.');
        $this->redirect('/employe/plats');
    }

    public function deletePlat(string $id): void
    {
        $this->verifyCsrf();
        PlatRepository::delete((int) $id);
        $this->flash('success', 'Plat supprimé.');
        $this->redirect('/employe/plats');
    }

    // ----- Horaires -----
    public function horaires(): void
    {
        $this->view('employe/horaires', [
            'pageTitle' => 'Gérer les horaires',
            'horaires'  => HoraireRepository::all(),
        ]);
    }

    public function updateHoraire(string $id): void
    {
        $this->verifyCsrf();
        HoraireRepository::update(
            (int) $id,
            (string) $this->input('jour'),
            (string) $this->input('heure_ouverture'),
            (string) $this->input('heure_fermeture'),
        );
        $this->flash('success', 'Horaire mis à jour.');
        $this->redirect('/employe/horaires');
    }

    // ----- Avis (modération via AvisService) -----
    public function avis(): void
    {
        $this->view('employe/avis', [
            'pageTitle' => 'Avis en attente',
            'avis'      => $this->avisService->listerEnAttente(),
        ]);
    }

    public function validerAvis(string $id): void
    {
        $this->verifyCsrf();
        $this->avisService->moderer((int) $id, true, (int) Auth::id());
        $this->flash('success', 'Avis validé et publié.');
        $this->redirect('/employe/avis');
    }

    public function refuserAvis(string $id): void
    {
        $this->verifyCsrf();
        $this->avisService->moderer((int) $id, false, (int) Auth::id());
        $this->flash('success', 'Avis refusé.');
        $this->redirect('/employe/avis');
    }
}
