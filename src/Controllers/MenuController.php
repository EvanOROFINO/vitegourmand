<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\MenuRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\ThemeRepository;

final class MenuController extends Controller
{
    public function index(): void
    {
        $this->view('menu/index', [
            'pageTitle' => 'Nos menus',
            'menus'     => MenuRepository::search(),
            'themes'    => ThemeRepository::all(),
            'regimes'   => RegimeRepository::all(),
        ]);
    }

    /**
     * Endpoint JSON pour les filtres dynamiques (requête JS sans rechargement de page).
     */
    public function apiSearch(): void
    {
        $filters = [
            'prix_max'  => isset($_GET['prix_max']) && $_GET['prix_max'] !== '' ? (float) $_GET['prix_max'] : null,
            'prix_min'  => isset($_GET['prix_min']) && $_GET['prix_min'] !== '' ? (float) $_GET['prix_min'] : null,
            'theme'     => $_GET['theme'] ?? null,
            'regime'    => $_GET['regime'] ?? null,
            'personnes' => isset($_GET['personnes']) && $_GET['personnes'] !== '' ? (int) $_GET['personnes'] : null,
        ];
        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $this->json(MenuRepository::search($filters));
    }

    public function show(string $id): void
    {
        $menu = MenuRepository::find((int) $id);
        if (!$menu) {
            http_response_code(404);
            require __DIR__ . '/../../views/errors/404.php';
            return;
        }

        $this->view('menu/show', [
            'pageTitle' => $menu['titre'],
            'menu'      => $menu,
        ]);
    }
}
