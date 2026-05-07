<?php /** @var array $horaires */ ?>
<section class="section">
    <div class="container container-narrow">
        <h1>Gérer les horaires</h1>
        <?php foreach ($horaires as $h): ?>
            <form method="post" action="/employe/horaires/<?= (int) $h['horaire_id'] ?>" class="form filtres">
                <?= csrf_field() ?>
                <div class="filtres-grid">
                    <div class="form-field">
                        <label>Jour</label>
                        <input type="text" name="jour" value="<?= e($h['jour']) ?>" readonly>
                    </div>
                    <div class="form-field">
                        <label>Ouverture</label>
                        <input type="text" name="heure_ouverture" value="<?= e($h['heure_ouverture']) ?>">
                    </div>
                    <div class="form-field">
                        <label>Fermeture</label>
                        <input type="text" name="heure_fermeture" value="<?= e($h['heure_fermeture']) ?>">
                    </div>
                    <div class="form-field form-field-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</section>
