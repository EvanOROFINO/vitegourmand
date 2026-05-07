<?php
/**
 * Configuration centrale de l'application Vite & Gourmand.
 * Charge les variables d'environnement depuis .env et les expose via env().
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($GLOBALS['__vg_dotenv_loaded'])) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
    $GLOBALS['__vg_dotenv_loaded'] = true;
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        return match (strtolower((string) $value)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default            => $value,
        };
    }
}

return [
    'app' => [
        'name'   => env('APP_NAME', 'Vite & Gourmand'),
        'env'    => env('APP_ENV', 'production'),
        'url'    => env('APP_URL', 'http://localhost:8000'),
        'debug'  => (bool) env('APP_DEBUG', false),
        'secret' => env('APP_SECRET', ''),
    ],
    'db' => [
        'host'     => env('DB_HOST', 'localhost'),
        'port'     => (int) env('DB_PORT', 3306),
        'name'     => env('DB_NAME', 'vitegourmand'),
        'user'     => env('DB_USER', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset'  => 'utf8mb4',
    ],
    'mongo' => [
        'uri' => env('MONGO_URI', 'mongodb://localhost:27017'),
        'db'  => env('MONGO_DB', 'vitegourmand_nosql'),
    ],
    'mail' => [
        'host'      => env('MAIL_HOST', 'smtp.gmail.com'),
        'port'      => (int) env('MAIL_PORT', 587),
        'username'  => env('MAIL_USERNAME', ''),
        'password'  => env('MAIL_PASSWORD', ''),
        'from'      => env('MAIL_FROM_ADDRESS', 'noreply@vitegourmand.fr'),
        'from_name' => env('MAIL_FROM_NAME', 'Vite & Gourmand'),
        'contact_to'=> env('CONTACT_TO', 'contact@vitegourmand.fr'),
    ],
    'business' => [
        'ville_locale'         => 'Bordeaux',
        'frais_livraison_base' => 5.00,
        'frais_par_km'         => 0.59,
        'reduction_seuil'      => 5,
        'reduction_taux'       => 0.10,
        'frais_non_restitution'=> 600.00,
    ],
];
