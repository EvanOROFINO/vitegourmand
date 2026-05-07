<?php /** @var string $next */ ?>
<section class="section">
    <div class="container container-narrow">
        <h1>Connexion</h1>

        <form method="post" action="/login" class="form" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="next" value="<?= e($next) ?>">

            <div class="form-field">
                <label for="email">Adresse e-mail <span class="required">*</span></label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="form-field">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="text-center mt-3">
            <a href="/mot-de-passe-oublie">Mot de passe oublié ?</a>
        </p>
        <p class="text-center">
            Pas encore de compte ? <a href="/register">Créez-en un</a>
        </p>
    </div>
</section>
