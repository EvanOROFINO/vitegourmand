<?php /** @var array $menu, $user */ ?>
<section class="section">
    <div class="container container-narrow">
        <h1>Commander : <?= e($menu['titre']) ?></h1>

        <?php if (!empty($menu['conditions_menu'])): ?>
            <div class="alert alert-warning">
                <strong>⚠ Conditions de ce menu :</strong> <?= nl2br(e($menu['conditions_menu'])) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/commander" class="form" id="commande-form">
            <?= csrf_field() ?>
            <input type="hidden" name="menu_id" value="<?= (int) $menu['menu_id'] ?>">

            <h2>Vos informations</h2>
            <div class="form-row">
                <div class="form-field">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" value="<?= e($user['prenom']) ?>" readonly>
                </div>
                <div class="form-field">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" value="<?= e($user['nom']) ?>" readonly>
                </div>
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" value="<?= e($user['email']) ?>" readonly>
                </div>
                <div class="form-field">
                    <label for="telephone">GSM</label>
                    <input type="tel" id="telephone" value="<?= e($user['telephone']) ?>" readonly>
                </div>
            </div>
            <p class="form-help">
                Pour modifier ces informations, rendez-vous sur <a href="/mon-espace/profil">votre profil</a>.
            </p>

            <h2 class="mt-4">Détails de la prestation</h2>

            <div class="form-row">
                <div class="form-field">
                    <label for="date_prestation">Date de la prestation <span class="required">*</span></label>
                    <input type="date" id="date_prestation" name="date_prestation" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
                <div class="form-field">
                    <label for="heure_livraison">Heure souhaitée <span class="required">*</span></label>
                    <input type="time" id="heure_livraison" name="heure_livraison" required>
                </div>
            </div>

            <div class="form-field">
                <label for="adresse_livraison">Adresse de livraison <span class="required">*</span></label>
                <input type="text" id="adresse_livraison" name="adresse_livraison" required value="<?= e($user['adresse_postale']) ?>">
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="ville_livraison">Ville <span class="required">*</span></label>
                    <input type="text" id="ville_livraison" name="ville_livraison" required value="<?= e($user['ville']) ?>">
                </div>
                <div class="form-field">
                    <label for="distance_km">Distance depuis Bordeaux (km)</label>
                    <input type="number" id="distance_km" name="distance_km" min="0" step="0.1" value="0">
                    <small class="form-help">Laissez 0 si la livraison est à Bordeaux. Sinon, indiquer les km.</small>
                </div>
            </div>

            <h2 class="mt-4">Nombre de convives</h2>
            <div class="form-field">
                <label for="nombre_personne">Nombre de personnes <span class="required">*</span></label>
                <input type="number" id="nombre_personne" name="nombre_personne" required min="<?= (int) $menu['nombre_personne_minimum'] ?>" value="<?= (int) $menu['nombre_personne_minimum'] ?>">
                <small class="form-help">
                    Minimum requis : <strong><?= (int) $menu['nombre_personne_minimum'] ?> personnes</strong>.
                    Une réduction de 10% s'applique automatiquement si vous commandez pour
                    <strong><?= (int) $menu['nombre_personne_minimum'] + 5 ?> personnes ou plus</strong>.
                </small>
            </div>

            <h2 class="mt-4">Récapitulatif</h2>
            <div id="recapitulatif" class="recap" data-prix-personne="<?= (float) $menu['prix_par_personne'] ?>" data-min="<?= (int) $menu['nombre_personne_minimum'] ?>">
                <div class="recap-line"><span>Menu (<span id="r-nb">0</span> personnes ×&nbsp;<?= format_prix((float) $menu['prix_par_personne']) ?>)</span><strong id="r-menu">0,00 €</strong></div>
                <div class="recap-line" id="r-reduc-line" hidden><span>Réduction 10% (5+ pers. au-dessus du minimum)</span><strong id="r-reduc">−0,00 €</strong></div>
                <div class="recap-line" id="r-livr-line" hidden><span>Frais de livraison (5€ + 0,59€/km hors Bordeaux)</span><strong id="r-livr">0,00 €</strong></div>
                <div class="recap-line recap-total"><span>Total à régler</span><strong id="r-total">0,00 €</strong></div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block mt-4">
                <i class="bi bi-check-circle-fill" aria-hidden="true"></i> Valider la commande
            </button>
        </form>
    </div>
</section>
