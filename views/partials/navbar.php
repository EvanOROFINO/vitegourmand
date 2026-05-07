<?php
use App\Core\Auth;
$user = Auth::user();
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<nav class="navbar" aria-label="Menu principal">
    <div class="navbar-container">
        <a href="/" class="navbar-brand">
            <i class="bi bi-cup-hot-fill" aria-hidden="true"></i> Vite &amp; Gourmand
        </a>
        <button class="navbar-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="nav-list">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>
        <ul id="nav-list" class="navbar-nav">
            <li><a href="/"        class="<?= $path === '/' ? 'active' : '' ?>">Accueil</a></li>
            <li><a href="/menus"   class="<?= str_starts_with((string) $path, '/menus') ? 'active' : '' ?>">Nos menus</a></li>
            <li><a href="/contact" class="<?= $path === '/contact' ? 'active' : '' ?>">Contact</a></li>

            <?php if ($user): ?>
                <li><a href="/mon-espace"><i class="bi bi-person-circle" aria-hidden="true"></i> <?= e($user['prenom']) ?></a></li>
                <?php if (Auth::hasRole('employe')): ?>
                    <li><a href="/employe">Espace employé</a></li>
                <?php endif; ?>
                <?php if (Auth::hasRole('administrateur')): ?>
                    <li><a href="/admin">Admin</a></li>
                <?php endif; ?>
                <li>
                    <form method="post" action="/logout" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-secondary btn-sm">Déconnexion</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/login" class="btn-cta">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
