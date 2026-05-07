<?php /** @var array $user */ ?>
<section class="section">
    <div class="container container-narrow">
        <h1>Mon profil</h1>

        <form method="post" action="/mon-espace/profil" class="form">
            <?= csrf_field() ?>

            <div class="form-row">
                <div class="form-field">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" required value="<?= e($user['prenom']) ?>">
                </div>
                <div class="form-field">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" required value="<?= e($user['nom']) ?>">
                </div>
            </div>

            <div class="form-field">
                <label for="email">E-mail (non modifiable)</label>
                <input type="email" id="email" value="<?= e($user['email']) ?>" readonly>
            </div>

            <div class="form-field">
                <label for="telephone">GSM <span class="required">*</span></label>
                <input type="tel" id="telephone" name="telephone" required value="<?= e($user['telephone']) ?>">
            </div>

            <div class="form-field">
                <label for="adresse_postale">Adresse postale <span class="required">*</span></label>
                <input type="text" id="adresse_postale" name="adresse_postale" required value="<?= e($user['adresse_postale']) ?>">
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" value="<?= e($user['ville']) ?>">
                </div>
                <div class="form-field">
                    <label for="pays">Pays</label>
                    <input type="text" id="pays" name="pays" value="<?= e($user['pays']) ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
</section>
