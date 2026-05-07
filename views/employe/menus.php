<?php /** @var array $menus */ ?>
<section class="section">
    <div class="container">
        <div class="page-header">
            <h1>Gérer les menus</h1>
            <a href="/employe/menus/nouveau" class="btn btn-primary"><i class="bi bi-plus-lg" aria-hidden="true"></i> Nouveau menu</a>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>Titre</th><th>Thème</th><th>Régime</th><th>Min. pers.</th><th>Prix</th><th>Stock</th><th>Actif</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $m): ?>
                        <tr>
                            <td><?= e($m['titre']) ?></td>
                            <td><?= e($m['theme_libelle']) ?></td>
                            <td><?= e($m['regime_libelle']) ?></td>
                            <td><?= (int) $m['nombre_personne_minimum'] ?></td>
                            <td><?= format_prix((float) $m['prix_par_personne']) ?></td>
                            <td><?= (int) $m['quantite_restante'] ?></td>
                            <td><?= $m['actif'] ? '✅' : '❌' ?></td>
                            <td>
                                <a href="/employe/menus/<?= (int) $m['menu_id'] ?>/editer" class="btn btn-sm btn-secondary">Modifier</a>
                                <form method="post" action="/employe/menus/<?= (int) $m['menu_id'] ?>/supprimer" class="inline-form" onsubmit="return confirm('Désactiver ce menu ?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Désactiver</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
