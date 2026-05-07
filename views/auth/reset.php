<?php /** @var string $token */ ?>
<section class="section">
    <div class="container container-narrow">
        <h1>Nouveau mot de passe</h1>

        <form method="post" action="/reinitialiser" class="form">
            <?= csrf_field() ?>
            <input type="hidden" name="token" value="<?= e($token) ?>">

            <div class="form-field">
                <label for="password">Nouveau mot de passe <span class="required">*</span></label>
                <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password" aria-describedby="password-help">
                <small id="password-help" class="form-help">10 caractères, 1 maj, 1 min, 1 chiffre, 1 caractère spécial.</small>
            </div>

            <div class="form-field">
                <label for="password_confirm">Confirmer <span class="required">*</span></label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="10" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Modifier mon mot de passe</button>
        </form>
    </div>
</section>
