<section class="section">
    <div class="container container-narrow">
        <h1>Créer un compte</h1>

        <form method="post" action="/register" class="form" novalidate>
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-field">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-field">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" required>
                </div>
            </div>

            <div class="form-field">
                <label for="email">Adresse e-mail <span class="required">*</span></label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>

            <div class="form-field">
                <label for="telephone">Téléphone (GSM) <span class="required">*</span></label>
                <input type="tel" id="telephone" name="telephone" required pattern="[0-9 +]+" autocomplete="tel">
            </div>

            <div class="form-field">
                <label for="adresse_postale">Adresse postale <span class="required">*</span></label>
                <input type="text" id="adresse_postale" name="adresse_postale" required autocomplete="street-address">
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="Bordeaux" autocomplete="address-level2">
                </div>
                <div class="form-field">
                    <label for="pays">Pays</label>
                    <input type="text" id="pays" name="pays" value="France" autocomplete="country-name">
                </div>
            </div>

            <div class="form-field">
                <label for="password">Mot de passe <span class="required">*</span></label>
                <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password" aria-describedby="password-help">
                <small id="password-help" class="form-help">10 caractères minimum, dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.</small>
            </div>

            <div class="form-field">
                <label for="password_confirm">Confirmer le mot de passe <span class="required">*</span></label>
                <input type="password" id="password_confirm" name="password_confirm" required minlength="10" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block">Créer mon compte</button>
        </form>

        <p class="text-center mt-3">
            Déjà un compte ? <a href="/login">Connectez-vous</a>
        </p>
    </div>
</section>
