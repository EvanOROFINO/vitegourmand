<?php /** @var array $avis */ ?>
<section class="section">
    <div class="container">
        <h1>Avis en attente de validation</h1>

        <?php if (empty($avis)): ?>
            <div class="empty-state">
                <i class="bi bi-check-circle" aria-hidden="true"></i>
                <p>Aucun avis en attente.</p>
            </div>
        <?php else: ?>
            <?php foreach ($avis as $a): ?>
                <article class="avis-moderation">
                    <header>
                        <strong><?= e($a['prenom']) ?> <?= e($a['nom']) ?></strong>
                        <span class="text-muted"><?= e($a['email']) ?></span>
                        <span class="avis-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?><i class="bi bi-star<?= $i <= $a['note'] ? '-fill' : '' ?>" aria-hidden="true"></i><?php endfor; ?>
                        </span>
                    </header>
                    <blockquote>« <?= e($a['description']) ?> »</blockquote>
                    <footer>
                        <form method="post" action="/employe/avis/<?= (int) $a['avis_id'] ?>/valider" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-primary">Valider</button>
                        </form>
                        <form method="post" action="/employe/avis/<?= (int) $a['avis_id'] ?>/refuser" class="inline-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Refuser</button>
                        </form>
                    </footer>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
