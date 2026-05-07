<?php /** @var array $commandes */ ?>
<section class="section">
    <div class="container">
        <h1>Mes commandes</h1>

        <?php if (empty($commandes)): ?>
            <div class="empty-state">
                <i class="bi bi-bag" aria-hidden="true"></i>
                <p>Vous n'avez pas encore passé de commande.</p>
                <a href="/menus" class="btn btn-primary">Découvrir nos menus</a>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>N°</th><th>Menu</th><th>Date prestation</th><th>Convives</th><th>Total</th><th>Statut</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commandes as $c): ?>
                            <tr>
                                <td><?= e($c['numero_commande']) ?></td>
                                <td><?= e($c['menu_titre']) ?></td>
                                <td><?= e(format_date_fr($c['date_prestation'])) ?> à <?= e(substr($c['heure_livraison'], 0, 5)) ?></td>
                                <td><?= (int) $c['nombre_personne'] ?></td>
                                <td><?= format_prix((float) $c['prix_total']) ?></td>
                                <td><span class="badge"><?= e(format_statut($c['statut'])) ?></span></td>
                                <td><a href="/mon-espace/commandes/<?= urlencode($c['numero_commande']) ?>" class="btn btn-secondary btn-sm">Détail</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
