<?php /** @var array $commandes; @var int $nbCommandes */ use App\Core\Auth; $u = Auth::user(); ?>
<section class="section">
    <div class="container">
        <h1>Bonjour <?= e($u['prenom']) ?> 👋</h1>
        <p>Voici votre espace personnel.</p>

        <div class="grid grid-3 mt-4">
            <a href="/mon-espace/commandes" class="dashboard-card">
                <i class="bi bi-bag-fill" aria-hidden="true"></i>
                <h2>Mes commandes</h2>
                <p><?= $nbCommandes ?> commande<?= $nbCommandes > 1 ? 's' : '' ?></p>
            </a>
            <a href="/mon-espace/profil" class="dashboard-card">
                <i class="bi bi-person-fill" aria-hidden="true"></i>
                <h2>Mon profil</h2>
                <p>Modifier mes informations</p>
            </a>
            <a href="/menus" class="dashboard-card">
                <i class="bi bi-cup-hot-fill" aria-hidden="true"></i>
                <h2>Découvrir les menus</h2>
                <p>Passer une nouvelle commande</p>
            </a>
        </div>

        <?php if (!empty($commandes)): ?>
            <h2 class="mt-5">Dernières commandes</h2>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N°</th><th>Menu</th><th>Date prestation</th><th>Total</th><th>Statut</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($commandes, 0, 5) as $c): ?>
                            <tr>
                                <td><?= e($c['numero_commande']) ?></td>
                                <td><?= e($c['menu_titre']) ?></td>
                                <td><?= e(format_date_fr($c['date_prestation'])) ?></td>
                                <td><?= format_prix((float) $c['prix_total']) ?></td>
                                <td><span class="badge"><?= e(format_statut($c['statut'])) ?></span></td>
                                <td><a href="/mon-espace/commandes/<?= urlencode($c['numero_commande']) ?>">Voir</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
