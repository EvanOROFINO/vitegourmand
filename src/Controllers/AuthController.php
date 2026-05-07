<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Security;
use App\Models\User;
use PDOException;

final class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/mon-espace');
        }
        $this->view('auth/login', [
            'pageTitle' => 'Connexion',
            'next'      => $_GET['next'] ?? '/mon-espace',
        ]);
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = $this->input('email');
        $password = $_POST['password'] ?? '';
        $next     = $_POST['next'] ?? '/mon-espace';

        $user = User::findByEmail((string) $email);
        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            $this->flash('error', 'Identifiants incorrects ou compte désactivé.');
            $this->redirect('/login');
        }

        Auth::login($user);
        $this->flash('success', 'Bienvenue ' . htmlspecialchars($user['prenom']) . ' !');
        $this->redirect($next);
    }

    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/mon-espace');
        }
        $this->view('auth/register', ['pageTitle' => 'Créer un compte']);
    }

    public function register(): void
    {
        $this->verifyCsrf();

        $data = [
            'email'           => $this->input('email'),
            'prenom'          => $this->input('prenom'),
            'nom'             => $this->input('nom'),
            'telephone'       => $this->input('telephone'),
            'adresse_postale' => $this->input('adresse_postale'),
            'ville'           => $this->input('ville') ?: 'Bordeaux',
        ];
        $password        = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validations
        foreach (['email','prenom','nom','telephone','adresse_postale'] as $key) {
            if (empty($data[$key])) {
                $this->flash('error', 'Tous les champs sont obligatoires.');
                $this->redirect('/register');
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Adresse e-mail invalide.');
            $this->redirect('/register');
        }
        if ($password !== $passwordConfirm) {
            $this->flash('error', 'Les mots de passe ne correspondent pas.');
            $this->redirect('/register');
        }
        if ($err = Security::validatePasswordStrength($password)) {
            $this->flash('error', $err);
            $this->redirect('/register');
        }

        $data['password'] = Security::hashPassword($password);

        try {
            User::create($data);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $this->flash('error', 'Cette adresse e-mail est déjà utilisée.');
            } else {
                $this->flash('error', 'Erreur lors de la création du compte.');
            }
            $this->redirect('/register');
        }

        // Mail de bienvenue
        Mailer::send(
            (string) $data['email'],
            'Bienvenue chez Vite & Gourmand !',
            '<h1>Bienvenue ' . e($data['prenom']) . ' !</h1>
             <p>Votre compte a bien été créé. Vous pouvez désormais vous connecter et passer commande sur notre site.</p>
             <p>À bientôt,<br>L\'équipe Vite & Gourmand</p>'
        );

        $this->flash('success', 'Compte créé avec succès. Vous pouvez vous connecter.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->verifyCsrf();
        Auth::logout();
        $this->redirect('/');
    }

    public function showForgot(): void
    {
        $this->view('auth/forgot', ['pageTitle' => 'Mot de passe oublié']);
    }

    public function sendReset(): void
    {
        $this->verifyCsrf();
        $email = $this->input('email');
        $user  = User::findByEmail((string) $email);

        // Réponse identique pour ne pas révéler l'existence d'un compte
        if ($user) {
            $token   = Security::randomToken(32);
            $expires = date('Y-m-d H:i:s', time() + 3600);
            User::setResetToken((int) $user['utilisateur_id'], $token, $expires);

            $config = require __DIR__ . '/../../config/config.php';
            $link   = rtrim($config['app']['url'], '/') . '/reinitialiser/' . $token;

            Mailer::send(
                $user['email'],
                'Réinitialisation de votre mot de passe',
                '<h1>Réinitialisation de votre mot de passe</h1>
                 <p>Bonjour ' . e($user['prenom']) . ',</p>
                 <p>Cliquez sur le lien suivant pour réinitialiser votre mot de passe (lien valable 1 heure) :</p>
                 <p><a href="' . e($link) . '">' . e($link) . '</a></p>
                 <p>Si vous n\'avez pas demandé cette réinitialisation, ignorez ce mail.</p>'
            );
        }

        $this->flash('success', 'Si un compte existe avec cette adresse, un mail vient d\'être envoyé.');
        $this->redirect('/login');
    }

    public function showReset(string $token): void
    {
        $user = User::findByResetToken($token);
        if (!$user) {
            $this->flash('error', 'Lien invalide ou expiré.');
            $this->redirect('/login');
        }
        $this->view('auth/reset', ['pageTitle' => 'Nouveau mot de passe', 'token' => $token]);
    }

    public function doReset(): void
    {
        $this->verifyCsrf();
        $token           = $_POST['token'] ?? '';
        $password        = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        $user = User::findByResetToken($token);
        if (!$user) {
            $this->flash('error', 'Lien invalide ou expiré.');
            $this->redirect('/login');
        }
        if ($password !== $passwordConfirm) {
            $this->flash('error', 'Les mots de passe ne correspondent pas.');
            $this->redirect('/reinitialiser/' . urlencode($token));
        }
        if ($err = Security::validatePasswordStrength($password)) {
            $this->flash('error', $err);
            $this->redirect('/reinitialiser/' . urlencode($token));
        }

        User::updatePassword((int) $user['utilisateur_id'], Security::hashPassword($password));
        $this->flash('success', 'Mot de passe modifié. Vous pouvez vous connecter.');
        $this->redirect('/login');
    }
}
