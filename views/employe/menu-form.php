<?php /** @var ?array $menu, array $themes, $regimes, $plats */
$action = $menu ? '/employe/menus/' . (int) $menu['menu_id'] : '/employe/menus';
$selectedPlats = $menu ? array_column($menu['plats'], 'plat_id') : [];
?>
<section class="section">
    <div class="container container-narrow">
        <h1><?= $menu ? 'Modifier le menu' : 'Nouveau menu' ?></h1>

        <form method="post" action="<?= e($action) ?>" class="form">
            <?= csrf_field() ?>

            <div class="form-field">
                <label for="titre">Titre <span class="required">*</span></label>
                <input type="text" id="titre" name="titre" required value="<?= e($menu['titre'] ?? '') ?>">
            </div>

            <div class="form-field">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" rows="4" required><?= e($menu['description'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="theme_id">Thème <span class="required">*</span></label>
                    <select id="theme_id" name="theme_id" required>
                        <?php foreach ($themes as $t): ?>
                            <option value="<?= (int) $t['theme_id'] ?>" <?= ($menu['theme_id'] ?? null) == $t['theme_id'] ? 'selected' : '' ?>><?= e($t['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="regime_id">Régime <span class="required">*</span></label>
                    <select id="regime_id" name="regime_id" required>
                        <?php foreach ($regimes as $r): ?>
                            <option value="<?= (int) $r['regime_id'] ?>" <?= ($menu['regime_id'] ?? null) == $r['regime_id'] ? 'selected' : '' ?>><?= e($r['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="nombre_personne_minimum">Nb personnes min. <span class="required">*</span></label>
                    <input type="number" id="nombre_personne_minimum" name="nombre_personne_minimum" min="1" required value="<?= (int) ($menu['nombre_personne_minimum'] ?? 4) ?>">
                </div>
                <div class="form-field">
                    <label for="prix_par_personne">Prix / pers. (€) <span class="required">*</span></label>
                    <input type="number" id="prix_par_personne" name="prix_par_personne" step="0.01" min="0" required value="<?= e((string) ($menu['prix_par_personne'] ?? 0)) ?>">
                </div>
                <div class="form-field">
                    <label for="quantite_restante">Stock</label>
                    <input type="number" id="quantite_restante" name="quantite_restante" min="0" value="<?= (int) ($menu['quantite_restante'] ?? 0) ?>">
                </div>
            </div>

            <div class="form-field">
                <label for="conditions_menu">Conditions</label>
                <textarea id="conditions_menu" name="conditions_menu" rows="3"><?= e($menu['conditions_menu'] ?? '') ?></textarea>
            </div>

            <div class="form-field">
                <label>Plats inclus</label>
                <div class="checkbox-grid">
                    <?php foreach ($plats as $p): ?>
                        <label class="checkbox-item">
                            <input type="checkbox" name="plats[]" value="<?= (int) $p['plat_id'] ?>" <?= in_array($p['plat_id'], $selectedPlats) ? 'checked' : '' ?>>
                            <?= e($p['titre']) ?> <small>(<?= e($p['type']) ?>)</small>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-field">
                <label class="checkbox-item">
                    <input type="checkbox" name="actif" <?= ($menu['actif'] ?? true) ? 'checked' : '' ?>>
                    Menu actif (visible aux clients)
                </label>
            </div>

            <button type="submit" class="btn btn-primary"><?= $menu ? 'Enregistrer' : 'Créer le menu' ?></button>
            <a href="/employe/menus" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</section>
