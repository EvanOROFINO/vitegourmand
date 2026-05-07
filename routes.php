<?php
/**
 * Définition des routes de l'application Vite & Gourmand.
 * Format : $router->method('/chemin', [Controller::class, 'methode']);
 */

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\MenuController;
use App\Controllers\ContactController;
use App\Controllers\CommandeController;
use App\Controllers\UserController;
use App\Controllers\EmployeController;
use App\Controllers\AdminController;

// ---------- Pages publiques ----------
$router->get('/',                  [HomeController::class, 'index']);
$router->get('/mentions-legales',  [HomeController::class, 'mentionsLegales']);
$router->get('/cgv',               [HomeController::class, 'cgv']);
$router->get('/contact',           [ContactController::class, 'show']);
$router->post('/contact',          [ContactController::class, 'send']);

// ---------- Authentification ----------
$router->get('/login',                  [AuthController::class, 'showLogin']);
$router->post('/login',                 [AuthController::class, 'login']);
$router->get('/register',               [AuthController::class, 'showRegister']);
$router->post('/register',              [AuthController::class, 'register']);
$router->post('/logout',                [AuthController::class, 'logout']);
$router->get('/mot-de-passe-oublie',    [AuthController::class, 'showForgot']);
$router->post('/mot-de-passe-oublie',   [AuthController::class, 'sendReset']);
$router->get('/reinitialiser/{token}',  [AuthController::class, 'showReset']);
$router->post('/reinitialiser',         [AuthController::class, 'doReset']);

// ---------- Menus (visiteur + utilisateur) ----------
$router->get('/menus',         [MenuController::class, 'index']);
$router->get('/api/menus',     [MenuController::class, 'apiSearch']); // filtres dynamiques
$router->get('/menus/{id}',    [MenuController::class, 'show']);

// ---------- Commande (utilisateur connecté) ----------
$router->get('/commander/{menu_id}', [CommandeController::class, 'showForm']);
$router->post('/commander',          [CommandeController::class, 'submit']);
$router->post('/api/calculer-prix',  [CommandeController::class, 'apiCalculPrix']);

// ---------- Espace utilisateur ----------
$router->get('/mon-espace',                              [UserController::class, 'dashboard']);
$router->get('/mon-espace/profil',                       [UserController::class, 'profil']);
$router->post('/mon-espace/profil',                      [UserController::class, 'updateProfil']);
$router->get('/mon-espace/commandes',                    [UserController::class, 'commandes']);
$router->get('/mon-espace/commandes/{numero}',           [UserController::class, 'commandeDetail']);
$router->post('/mon-espace/commandes/{numero}/annuler',  [UserController::class, 'annulerCommande']);
$router->post('/mon-espace/commandes/{numero}/modifier', [UserController::class, 'modifierCommande']);
$router->get('/mon-espace/commandes/{numero}/avis',      [UserController::class, 'showAvisForm']);
$router->post('/mon-espace/commandes/{numero}/avis',     [UserController::class, 'submitAvis']);

// ---------- Espace employé ----------
$router->get('/employe',                                 [EmployeController::class, 'dashboard']);
$router->get('/employe/menus',                           [EmployeController::class, 'menus']);
$router->get('/employe/menus/nouveau',                   [EmployeController::class, 'newMenu']);
$router->post('/employe/menus',                          [EmployeController::class, 'createMenu']);
$router->get('/employe/menus/{id}/editer',               [EmployeController::class, 'editMenu']);
$router->post('/employe/menus/{id}',                     [EmployeController::class, 'updateMenu']);
$router->post('/employe/menus/{id}/supprimer',           [EmployeController::class, 'deleteMenu']);
$router->get('/employe/plats',                           [EmployeController::class, 'plats']);
$router->post('/employe/plats',                          [EmployeController::class, 'createPlat']);
$router->post('/employe/plats/{id}/supprimer',           [EmployeController::class, 'deletePlat']);
$router->get('/employe/horaires',                        [EmployeController::class, 'horaires']);
$router->post('/employe/horaires/{id}',                  [EmployeController::class, 'updateHoraire']);
$router->get('/employe/commandes',                       [EmployeController::class, 'commandes']);
$router->post('/employe/commandes/{numero}/statut',      [EmployeController::class, 'changerStatut']);
$router->post('/employe/commandes/{numero}/annuler',     [EmployeController::class, 'annulerCommande']);
$router->get('/employe/avis',                            [EmployeController::class, 'avis']);
$router->post('/employe/avis/{id}/valider',              [EmployeController::class, 'validerAvis']);
$router->post('/employe/avis/{id}/refuser',              [EmployeController::class, 'refuserAvis']);

// ---------- Espace administrateur ----------
$router->get('/admin',                                   [AdminController::class, 'dashboard']);
$router->get('/admin/employes',                          [AdminController::class, 'employes']);
$router->post('/admin/employes',                         [AdminController::class, 'createEmploye']);
$router->post('/admin/employes/{id}/desactiver',         [AdminController::class, 'desactiverEmploye']);
$router->post('/admin/employes/{id}/reactiver',          [AdminController::class, 'reactiverEmploye']);
$router->get('/admin/statistiques',                      [AdminController::class, 'statistiques']);
$router->get('/admin/api/stats',                         [AdminController::class, 'apiStats']);
$router->get('/admin/chiffre-affaires',                  [AdminController::class, 'chiffreAffaires']);
