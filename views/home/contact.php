<section class="section">
    <div class="container container-narrow">
        <h1>Nous contacter</h1>
        <p>Une question, une demande spécifique ? Remplissez le formulaire ci-dessous et nous vous répondrons rapidement.</p>

        <form method="post" action="/contact" class="form" novalidate>
            <?= csrf_field() ?>

            <div class="form-field">
                <label for="titre">Titre <span class="required">*</span></label>
                <input type="text" id="titre" name="titre" required maxlength="150">
            </div>

            <div class="form-field">
                <label for="email">Votre adresse e-mail <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-field">
                <label for="description">Votre message <span class="required">*</span></label>
                <textarea id="description" name="description" rows="6" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>
</section>
