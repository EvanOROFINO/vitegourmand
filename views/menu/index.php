<?php /** @var array $menus, $themes, $regimes */ ?>

<section class="section section-menus-header">
    <div class="container">
        <h1>Nos menus</h1>
        <p>Découvrez nos menus traiteurs, des plus classiques aux plus festifs.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <form id="filtres-form" class="filtres" aria-label="Filtres des menus">
            <h2 class="filtres-title">
                <i class="bi bi-funnel-fill" aria-hidden="true"></i> Filtrer
            </h2>

            <div class="filtres-grid">
                <div class="form-field">
                    <label for="prix_min">Prix min. (€/pers)</label>
                    <input type="number" id="prix_min" name="prix_min" min="0" step="1">
                </div>
                <div class="form-field">
                    <label for="prix_max">Prix max. (€/pers)</label>
                    <input type="number" id="prix_max" name="prix_max" min="0" step="1">
                </div>
                <div class="form-field">
                    <label for="theme">Thème</label>
                    <select id="theme" name="theme">
                        <option value="">Tous</option>
                        <?php foreach ($themes as $t): ?>
                            <option value="<?= (int) $t['theme_id'] ?>"><?= e($t['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="regime">Régime</label>
                    <select id="regime" name="regime">
                        <option value="">Tous</option>
                        <?php foreach ($regimes as $r): ?>
                            <option value="<?= (int) $r['regime_id'] ?>"><?= e($r['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="personnes">Convives</label>
                    <input type="number" id="personnes" name="personnes" min="1" step="1" placeholder="ex : 10">
                </div>
                <div class="form-field form-field-actions">
                    <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                </div>
            </div>
        </form>

        <div id="menus-grid" class="grid grid-3 mt-4">
            <?php require __DIR__ . '/_menu-cards.php'; ?>
        </div>
    </div>
</section>

<script>
window.MENUS_INITIAUX = <?= json_encode($menus, JSON_UNESCAPED_UNICODE) ?>;
</script>
