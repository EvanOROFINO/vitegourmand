<?php /** @var array $menu */ use App\Core\Auth; ?>

<section class="section">
    <div class="container">
        <a href="/menus" class="link-back"><i class="bi bi-arrow-left" aria-hidden="true"></i> Retour aux menus</a>

        <div class="menu-detail">
            <div class="menu-detail-images">
                <?php if (!empty($menu['images'])): ?>
                    <?php foreach ($menu['images'] as $img): ?>
                        <img src="<?= e($img['chemin']) ?>" alt="<?= e($img['alt_texte'] ?? $menu['titre']) ?>" loading="lazy">
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="menu-image-placeholder"><i class="bi bi-image" aria-hidden="true"></i></div>
                <?php endif; ?>
            </div>

            <div class="menu-detail-info">
                <div class="menu-card-tags">
                    <span class="badge"><?= e($menu['theme_libelle']) ?></span>
                    <span class="badge badge-secondary"><?= e($menu['regime_libelle']) ?></span>
                </div>
                <h1><?= e($menu['titre']) ?></h1>
                <p class="menu-detail-desc"><?= nl2br(e($menu['description'])) ?></p>

                <div class="menu-detail-meta">
                    <div><strong>À partir de :</strong> <?= (int) $menu['nombre_personne_minimum'] ?> personnes</div>
                    <div><strong>Prix :</strong> <?= format_prix((float) $menu['prix_par_personne']) ?> par personne</div>
                    <div><strong>Stock :</strong> <?= (int) $menu['quantite_restante'] ?> commandes restantes</div>
                </div>

                <?php if (!empty($menu['conditions_menu'])): ?>
                    <div class="alert alert-warning" role="note">
                        <strong><i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i> Conditions :</strong>
                        <?= nl2br(e($menu['conditions_menu'])) ?>
                    </div>
                <?php endif; ?>

                <?php if ((int) $menu['quantite_restante'] > 0): ?>
                    <?php if (Auth::check()): ?>
                        <a href="/commander/<?= (int) $menu['menu_id'] ?>" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus" aria-hidden="true"></i> Commander ce menu
                        </a>
                    <?php else: ?>
                        <div class="cta-connect">
                            <p>Vous devez être connecté pour passer commande.</p>
                            <a href="/login?next=/commander/<?= (int) $menu['menu_id'] ?>" class="btn btn-primary">Se connecter</a>
                            <a href="/register" class="btn btn-secondary">Créer un compte</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="alert alert-error">Ce menu n'est plus disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="mt-5">Composition du menu</h2>

        <?php
        $entrees  = array_filter($menu['plats'], fn($p) => $p['type'] === 'entree');
        $platsmain= array_filter($menu['plats'], fn($p) => $p['type'] === 'plat');
        $desserts = array_filter($menu['plats'], fn($p) => $p['type'] === 'dessert');
        ?>

        <div class="grid grid-3">
            <?php foreach ([['Entrées', $entrees], ['Plats', $platsmain], ['Desserts', $desserts]] as [$label, $list]): ?>
                <div class="plats-section">
                    <h3><?= e($label) ?></h3>
                    <?php if (empty($list)): ?>
                        <p class="text-muted">Aucun.</p>
                    <?php else: ?>
                        <ul class="plats-list">
                            <?php foreach ($list as $p): ?>
                                <li>
                                    <strong><?= e($p['titre']) ?></strong>
                                    <?php if (!empty($p['allergenes'])): ?>
                                        <small class="allergenes" aria-label="Allergènes">
                                            <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                                            <?= e($p['allergenes']) ?>
                                        </small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($menu['allergenes'])): ?>
            <div class="alert alert-info mt-4">
                <strong>Allergènes présents dans ce menu :</strong> <?= e(implode(', ', $menu['allergenes'])) ?>
            </div>
        <?php endif; ?>
    </div>
</section>
