<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\AvisRepository;

final class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'pageTitle' => 'Accueil',
            'avis'      => AvisRepository::valides(6),
        ]);
    }

    public function mentionsLegales(): void
    {
        $this->view('home/mentions-legales', ['pageTitle' => 'Mentions légales']);
    }

    public function cgv(): void
    {
        $this->view('home/cgv', ['pageTitle' => 'Conditions générales de vente']);
    }
}
