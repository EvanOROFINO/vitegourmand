<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Mailer;
use App\Core\Security;
use App\Models\Stats;
use App\Models\User;
use PDOException;

final class AdminController extends EmployeController
{
    public function __construct()
    {
        Auth::requireRole('administrateur');
    }

    public function dashboard(): void
    {
        $this->view('admin/dashboard', [
            'pageTitle' => 'Espace administrateur',
            'stats'     => Stats::commandesParMenu(),
        ]);
    }

    public function employes(): void
    {
        $this->view('admin/employes', [
            'pageTitle' => 'Gérer les employés',
            'employes'  => User::listEmployes(),
        ]);
    }

    public function createEmploye(): void
    {
        $this->verifyCsrf();
        $email    = $this->input('email');
        $password = $_POST['password'] ?? '';
        $prenom   = $this->input('prenom');
        $nom      = $this->input('nom');

        if (empty($email) || empty($prenom) || empty($nom)) {
            $this->flash('error', 'Tous les champs sont obligatoires.');
            $this->redirect('/admin/employes');
        }
        if ($err = Security::validatePasswordStrength($password)) {
            $this->flash('error', $err);
            $this->redirect('/admin/employes');
        }

        try {
            User::create([
                'email'           => $email,
                'password'        => Security::hashPassword($password),
                'prenom'          => $prenom,
                'nom'             => $nom,
                'telephone'       => '0000000000',
                'adresse_postale' => 'À compléter',
            ], ['employe', 'utilisateur']);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->flash('error', 'Cette adresse e-mail est déjà utilisée.');
            } else {
                $this->flash('error', 'Erreur lors de la création.');
            }
            $this->redirect('/admin/employes');
        }

        // Le mot de passe N'EST PAS envoyé par mail, conformément à l'énoncé
        Mailer::send(
            (string) $email,
            'Votre compte employé Vite & Gourmand',
            '<h1>Bienvenue dans l\'équipe !</h1>
             <p>Bonjour ' . e((string) $prenom) . ',</p>
             <p>Un compte employé a été créé pour vous sur la plateforme Vite & Gourmand.</p>
             <p>Pour obtenir votre mot de passe, merci de vous rapprocher de l\'administrateur.</p>'
        );

        Stats::log('employe_cree', ['email' => $email, 'admin_id' => Auth::id()]);
        $this->flash('success', 'Employé créé. Le mot de passe n\'est PAS envoyé par mail (à transmettre en main propre).');
        $this->redirect('/admin/employes');
    }

    public function desactiverEmploye(string $id): void
    {
        $this->verifyCsrf();
        User::setActive((int) $id, false);
        $this->flash('success', 'Compte désactivé.');
        $this->redirect('/admin/employes');
    }

    public function reactiverEmploye(string $id): void
    {
        $this->verifyCsrf();
        User::setActive((int) $id, true);
        $this->flash('success', 'Compte réactivé.');
        $this->redirect('/admin/employes');
    }

    public function statistiques(): void
    {
        $this->view('admin/statistiques', [
            'pageTitle' => 'Statistiques',
            'stats'     => Stats::commandesParMenu(),
        ]);
    }

    public function apiStats(): void
    {
        $this->json(Stats::commandesParMenu());
    }

    public function chiffreAffaires(): void
    {
        $debut   = $_GET['date_debut'] ?? null;
        $fin     = $_GET['date_fin']   ?? null;
        $menuId  = isset($_GET['menu_id']) && $_GET['menu_id'] !== '' ? (int) $_GET['menu_id'] : null;

        $this->view('admin/chiffre-affaires', [
            'pageTitle' => 'Chiffre d\'affaires',
            'stats'     => Stats::chiffreAffairesParMenu($debut, $fin, $menuId),
            'menus'     => \App\Models\Menu::search(),
            'filtres'   => ['debut' => $debut, 'fin' => $fin, 'menu_id' => $menuId],
        ]);
    }
}
