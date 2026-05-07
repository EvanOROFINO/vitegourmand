<?php /** @var array $commande */ ?>
<section class="section">
    <div class="container container-narrow">
        <h1>Donner mon avis</h1>
        <p>Pour la commande <strong><?= e($commande['numero_commande']) ?></strong> — <?= e($commande['menu_titre']) ?></p>

        <form method="post" action="/mon-espace/commandes/<?= urlencode($commande['numero_commande']) ?>/avis" class="form">
            <?= csrf_field() ?>

            <div class="form-field">
                <label>Votre note <span class="required">*</span></label>
                <div class="rating-input" role="radiogroup" aria-label="Note de 1 à 5 étoiles">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="rating-star">
                            <input type="radio" name="note" value="<?= $i ?>" required>
                            <i class="bi bi-star-fill" aria-hidden="true"></i>
                            <span class="visually-hidden"><?= $i ?> étoile<?= $i > 1 ? 's' : '' ?></span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="form-field">
                <label for="description">Votre commentaire <span class="required">*</span></label>
                <textarea id="description" name="description" rows="6" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Envoyer mon avis</button>
        </form>
    </div>
</section>
