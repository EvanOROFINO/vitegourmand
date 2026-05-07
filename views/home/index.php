<?php /** @var array $avis */ ?>

<section class="hero">
    <div class="container hero-content">
        <h1>Vite &amp; Gourmand</h1>
        <p class="hero-subtitle">Le savoir-faire d'un traiteur bordelais depuis 25 ans</p>
        <p class="hero-text">
            Julie et José vous accompagnent pour faire de chaque évènement un moment d'exception.
            Découvrez nos menus, sélectionnez vos plats et passez commande en ligne en quelques clics.
        </p>
        <div class="hero-cta">
            <a href="/menus" class="btn btn-primary btn-lg">Découvrir nos menus</a>
            <a href="/contact" class="btn btn-secondary btn-lg">Nous contacter</a>
        </div>
    </div>
</section>

<section class="section section-presentation" aria-labelledby="presentation-title">
    <div class="container">
        <h2 id="presentation-title">Une équipe à votre service</h2>
        <div class="grid grid-3">
            <article class="feature-card">
                <i class="bi bi-award-fill" aria-hidden="true"></i>
                <h3>25 ans d'expérience</h3>
                <p>Julie et José cuisinent ensemble depuis un quart de siècle, à Bordeaux et dans toute la Gironde.</p>
            </article>
            <article class="feature-card">
                <i class="bi bi-flower2" aria-hidden="true"></i>
                <h3>Produits frais et locaux</h3>
                <p>Chaque menu est composé à partir de produits soigneusement sélectionnés auprès de producteurs locaux.</p>
            </article>
            <article class="feature-card">
                <i class="bi bi-heart-fill" aria-hidden="true"></i>
                <h3>Sur-mesure</h3>
                <p>Adaptations selon vos régimes (végétarien, vegan, sans gluten…) et toutes vos contraintes.</p>
            </article>
        </div>
    </div>
</section>

<section class="section section-avis" aria-labelledby="avis-title">
    <div class="container">
        <h2 id="avis-title">Ce qu'en disent nos clients</h2>

        <?php if (empty($avis)): ?>
            <p class="text-muted text-center">Soyez le premier à laisser un avis après votre commande !</p>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($avis as $a): ?>
                    <article class="avis-card">
                        <div class="avis-stars" aria-label="Note : <?= (int) $a['note'] ?>/5">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= $i <= $a['note'] ? '-fill' : '' ?>" aria-hidden="true"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="avis-text">« <?= e($a['description']) ?> »</p>
                        <p class="avis-author"><strong><?= e($a['prenom']) ?> <?= e(strtoupper(substr($a['nom'], 0, 1))) ?>.</strong></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
