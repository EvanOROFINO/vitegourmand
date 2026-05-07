<?php /** @var array $commande, $historique; @var ?array $avis */ ?>
<section class="section">
    <div class="container">
        <a href="/mon-espace/commandes" class="link-back"><i class="bi bi-arrow-left" aria-hidden="true"></i> Toutes mes commandes</a>

        <h1>Commande <?= e($commande['numero_commande']) ?></h1>
        <p class="lead">Statut actuel : <span class="badge"><?= e(format_statut($commande['statut'])) ?></span></p>

        <div class="grid grid-2">
            <div>
                <h2>Détails</h2>
                <ul class="info-list">
                    <li><strong>Menu :</strong> <?= e($commande['menu_titre']) ?></li>
                    <li><strong>Date :</strong> <?= e(format_date_fr($commande['date_prestation'])) ?> à <?= e(substr($commande['heure_livraison'], 0, 5)) ?></li>
                    <li><strong>Lieu :</strong> <?= e($commande['adresse_livraison']) ?>, <?= e($commande['ville_livraison']) ?></li>
                    <li><strong>Convives :</strong> <?= (int) $commande['nombre_personne'] ?></li>
                </ul>

                <h2>Tarification</h2>
                <ul class="info-list">
                    <li><span>Menu :</span> <?= format_prix((float) $commande['prix_menu']) ?></li>
                    <?php if ((float) $commande['reduction'] > 0): ?>
                        <li><span>Réduction :</span> −<?= format_prix((float) $commande['reduction']) ?></li>
                    <?php endif; ?>
                    <?php if ((float) $commande['prix_livraison'] > 0): ?>
                        <li><span>Livraison :</span> <?= format_prix((float) $commande['prix_livraison']) ?></li>
                    <?php endif; ?>
                    <li><strong>Total : <?= format_prix((float) $commande['prix_total']) ?></strong></li>
                </ul>

                <?php if (!empty($commande['conditions_menu'])): ?>
                    <div class="alert alert-warning"><strong>Conditions :</strong> <?= nl2br(e($commande['conditions_menu'])) ?></div>
                <?php endif; ?>
            </div>

            <div>
                <h2>Suivi</h2>
                <ol class="timeline">
                    <?php foreach ($historique as $h): ?>
                        <li class="timeline-item <?= $h['statut'] === $commande['statut'] ? 'active' : 'done' ?>">
                            <div class="timeline-date"><?= e(date('d/m/Y H:i', strtotime($h['date_changement']))) ?></div>
                            <div class="timeline-text"><?= e(format_statut($h['statut'])) ?></div>
                            <?php if (!empty($h['commentaire'])): ?>
                                <div class="timeline-note"><?= e($h['commentaire']) ?></div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>

        <?php if ($commande['statut'] === 'en_attente'): ?>
            <div class="actions-bar mt-4">
                <form method="post" action="/mon-espace/commandes/<?= urlencode($commande['numero_commande']) ?>/annuler" onsubmit="return confirm('Annuler cette commande ?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="motif" value="Annulation par le client">
                    <button type="submit" class="btn btn-danger">Annuler la commande</button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($commande['statut'] === 'terminee'): ?>
            <?php if ($avis): ?>
                <div class="mt-4">
                    <h2>Votre avis</h2>
                    <p>
                        Note : <?php for ($i = 1; $i <= 5; $i++): ?><i class="bi bi-star<?= $i <= $avis['note'] ? '-fill' : '' ?>" aria-hidden="true"></i><?php endfor; ?><br>
                        Statut : <span class="badge"><?= $avis['statut'] === 'valide' ? 'Publié' : ($avis['statut'] === 'refuse' ? 'Refusé' : 'En modération') ?></span>
                    </p>
                    <blockquote>« <?= e($avis['description']) ?> »</blockquote>
                </div>
            <?php else: ?>
                <div class="mt-4">
                    <a href="/mon-espace/commandes/<?= urlencode($commande['numero_commande']) ?>/avis" class="btn btn-primary">
                        <i class="bi bi-star-fill" aria-hidden="true"></i> Donner mon avis
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
