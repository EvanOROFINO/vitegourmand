<?php
/**
 * Classe de base pour tous les controllers.
 * Fournit des helpers de rendu de vue, de redirection et de réponse JSON.
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new \RuntimeException("Vue introuvable : $view");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = __DIR__ . '/../../views/layouts/' . $layout . '.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function redirect(string $url, int $code = 302): never
    {
        header("Location: $url", true, $code);
        exit;
    }

    protected function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    protected function input(string $key, ?string $default = null): ?string
    {
        $value = $_POST[$key] ?? $_GET[$key] ?? $default;
        return is_string($value) ? Security::sanitize($value) : $default;
    }

    protected function verifyCsrf(): void
    {
        if (!Security::verifyCsrf($_POST['_csrf'] ?? null)) {
            http_response_code(419);
            $_SESSION['flash_error'] = 'Session expirée, veuillez réessayer.';
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION["flash_$type"] = $message;
    }
}
