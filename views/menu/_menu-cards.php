<?php
/** @var array $menus */
if (empty($menus)):
?>
    <p class="text-muted text-center" style="grid-column: 1 / -1;">Aucun menu ne correspond à vos critères.</p>
<?php else: foreach ($menus as $m): ?>
    <article class="menu-card">
        <?php if (!empty($m['image_principale'])): ?>
            <img src="<?= e($m['image_principale']) ?>" alt="" class="menu-card-image" loading="lazy">
        <?php endif; ?>
        <div class="menu-card-body">
            <div class="menu-card-tags">
                <?php if (!empty($m['theme_libelle'])): ?>
                    <span class="badge"><?= e($m['theme_libelle']) ?></span>
                <?php endif; ?>
                <?php if (!empty($m['regime_libelle'])): ?>
                    <span class="badge badge-secondary"><?= e($m['regime_libelle']) ?></span>
                <?php endif; ?>
            </div>
            <h3 class="menu-card-title"><?= e($m['titre']) ?></h3>
            <p class="menu-card-desc"><?= e(mb_substr($m['description'], 0, 120)) ?>…</p>
            <div class="menu-card-meta">
                <span><i class="bi bi-people-fill" aria-hidden="true"></i> <?= (int) $m['nombre_personne_minimum'] ?> pers. min.</span>
                <span class="menu-card-prix"><?= format_prix((float) $m['prix_par_personne']) ?> / pers.</span>
            </div>
            <a href="/menus/<?= (int) $m['menu_id'] ?>" class="btn btn-primary btn-block">Voir le détail</a>
        </div>
    </article>
<?php endforeach; endif; ?>
