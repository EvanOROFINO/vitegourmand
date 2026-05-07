<?php
/**
 * Gestion de l'authentification et des rôles utilisateur.
 *
 * Rôles Vite & Gourmand : utilisateur, employe, administrateur.
 */

declare(strict_types=1);

namespace App\Core;

final class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'      => (int) $user['utilisateur_id'],
            'email'   => $user['email'],
            'prenom'  => $user['prenom'],
            'nom'     => $user['nom'],
            'roles'   => $user['roles'] ?? [],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function hasRole(string $role): bool
    {
        return in_array($role, $_SESSION['user']['roles'] ?? [], true);
    }

    public static function hasAnyRole(array $roles): bool
    {
        $userRoles = $_SESSION['user']['roles'] ?? [];
        return (bool) array_intersect($roles, $userRoles);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            $_SESSION['flash_error'] = 'Vous devez être connecté pour accéder à cette page.';
            header('Location: /login?next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
            exit;
        }
    }

    public static function requireRole(string|array $roles): void
    {
        self::requireLogin();
        $roles = is_array($roles) ? $roles : [$roles];
        if (!self::hasAnyRole($roles)) {
            http_response_code(403);
            require __DIR__ . '/../../views/errors/403.php';
            exit;
        }
    }
}
