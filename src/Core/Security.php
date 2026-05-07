<?php
/**
 * Helpers de sécurité : CSRF, validation mot de passe, échappement.
 *
 * Règle métier mot de passe Vite & Gourmand : 10 caractères minimum,
 * au moins 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial.
 */

declare(strict_types=1);

namespace App\Core;

final class Security
{
    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(?string $token): bool
    {
        if ($token === null || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    public static function validatePasswordStrength(string $password): ?string
    {
        if (strlen($password) < 10) {
            return 'Le mot de passe doit contenir au moins 10 caractères.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Le mot de passe doit contenir au moins une majuscule.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            return 'Le mot de passe doit contenir au moins une minuscule.';
        }
        if (!preg_match('/\d/', $password)) {
            return 'Le mot de passe doit contenir au moins un chiffre.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Le mot de passe doit contenir au moins un caractère spécial.';
        }

        return null;
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function sanitize(?string $input): string
    {
        if ($input === null) {
            return '';
        }

        return trim(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input));
    }

    /**
     * Génère un token aléatoire (utilisé pour reset password, validation mail, etc).
     */
    public static function randomToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
