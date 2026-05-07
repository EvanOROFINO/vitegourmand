<?php use App\Models\Horaire; $horaires = Horaire::all(); ?>
<footer class="footer" role="contentinfo">
    <div class="footer-container">
        <div class="footer-col">
            <h3>Vite &amp; Gourmand</h3>
            <p>Traiteur événementiel à Bordeaux depuis 25 ans.</p>
            <p>
                <i class="bi bi-envelope" aria-hidden="true"></i>
                <a href="mailto:contact@vitegourmand.fr">contact@vitegourmand.fr</a>
            </p>
        </div>

        <div class="footer-col">
            <h3>Horaires</h3>
            <ul class="horaires-list">
                <?php foreach ($horaires as $h): ?>
                    <li>
                        <span><?= e($h['jour']) ?></span>
                        <span>
                            <?php if ($h['heure_ouverture'] === 'Fermé'): ?>
                                Fermé
                            <?php else: ?>
                                <?= e($h['heure_ouverture']) ?> – <?= e($h['heure_fermeture']) ?>
                            <?php endif; ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="footer-col">
            <h3>Informations</h3>
            <ul class="footer-links">
                <li><a href="/mentions-legales">Mentions légales</a></li>
                <li><a href="/cgv">Conditions générales de vente</a></li>
                <li><a href="/contact">Nous contacter</a></li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        © <?= date('Y') ?> Vite &amp; Gourmand. Projet pédagogique TP DWWM.
    </div>
</footer>
