<section class="section">
    <div class="container container-narrow">
        <h1>Mot de passe oublié</h1>
        <p>Saisissez votre adresse e-mail. Nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

        <form method="post" action="/mot-de-passe-oublie" class="form">
            <?= csrf_field() ?>
            <div class="form-field">
                <label for="email">Adresse e-mail <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Envoyer le lien</button>
        </form>
    </div>
</section>
