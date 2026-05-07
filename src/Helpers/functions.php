<?php
/**
 * Fonctions utilitaires globales utilisables dans toute l'application.
 */

declare(strict_types=1);

use App\Core\Security;

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return Security::escape($value);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(Security::csrfToken()) . '">';
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = '/'): string
    {
        $config = require __DIR__ . '/../../config/config.php';
        return rtrim($config['app']['url'], '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('flash')) {
    function flash(string $type): ?string
    {
        $key = "flash_$type";
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }
}

if (!function_exists('format_date_fr')) {
    function format_date_fr(string $date): string
    {
        $months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
        $ts = strtotime($date);
        if ($ts === false) {
            return $date;
        }
        return (int) date('j', $ts) . ' ' . $months[(int) date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }
}

if (!function_exists('format_prix')) {
    function format_prix(float $montant): string
    {
        return number_format($montant, 2, ',', ' ') . ' €';
    }
}

if (!function_exists('format_statut')) {
    /**
     * Convertit un statut technique en libellé humain.
     */
    function format_statut(string $statut): string
    {
        $map = [
            'en_attente'                => 'En attente',
            'accepte'                   => 'Acceptée',
            'en_preparation'            => 'En préparation',
            'en_cours_de_livraison'     => 'En cours de livraison',
            'livre'                     => 'Livrée',
            'en_attente_retour_materiel'=> 'En attente du retour de matériel',
            'terminee'                  => 'Terminée',
            'annulee'                   => 'Annulée',
        ];
        return $map[$statut] ?? ucfirst(str_replace('_', ' ', $statut));
    }
}

if (!function_exists('numero_commande')) {
    /**
     * Génère un numéro de commande unique au format CMD-YYYYMMDD-XXXX.
     */
    function numero_commande(): string
    {
        return 'CMD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}
