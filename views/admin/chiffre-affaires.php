<?php /** @var array $stats, $menus, $filtres */
$total = array_sum(array_column($stats, 'ca_total'));
$totalCmd = array_sum(array_column($stats, 'nb_commandes'));
?>
<section class="section">
    <div class="container">
        <h1>Chiffre d'affaires</h1>

        <form method="get" action="/admin/chiffre-affaires" class="filtres">
            <div class="filtres-grid">
                <div class="form-field">
                    <label for="date_debut">Du</label>
                    <input type="date" id="date_debut" name="date_debut" value="<?= e((string) ($filtres['debut'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label for="date_fin">Au</label>
                    <input type="date" id="date_fin" name="date_fin" value="<?= e((string) ($filtres['fin'] ?? '')) ?>">
                </div>
                <div class="form-field">
                    <label for="menu_id">Menu</label>
                    <select id="menu_id" name="menu_id">
                        <option value="">Tous</option>
                        <?php foreach ($menus as $m): ?>
                            <option value="<?= (int) $m['menu_id'] ?>" <?= ($filtres['menu_id'] ?? null) == $m['menu_id'] ? 'selected' : '' ?>><?= e($m['titre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field form-field-actions">
                    <button type="submit" class="btn btn-primary">Calculer</button>
                </div>
            </div>
        </form>

        <div class="grid grid-2 mt-4">
            <div class="kpi"><h3>Total commandes</h3><div class="kpi-value"><?= (int) $totalCmd ?></div></div>
            <div class="kpi"><h3>Chiffre d'affaires</h3><div class="kpi-value"><?= format_prix((float) $total) ?></div></div>
        </div>

        <h2 class="mt-5">Répartition par menu</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Menu</th><th>Nb commandes</th><th>CA</th></tr></thead>
                <tbody>
                    <?php foreach ($stats as $s): ?>
                        <tr>
                            <td><?= e($s['titre']) ?></td>
                            <td><?= (int) $s['nb_commandes'] ?></td>
                            <td><?= format_prix((float) $s['ca_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
