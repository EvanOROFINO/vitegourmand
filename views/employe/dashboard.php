<?php /** @var array $commandes; @var ?string $statut, $recherche; @var int $nbAvisAttente */ ?>
<section class="section">
    <div class="container">
        <h1>Espace employé — Commandes</h1>

        <div class="grid grid-3 mb-4">
            <a href="/employe/menus"    class="dashboard-card"><i class="bi bi-cup-hot-fill" aria-hidden="true"></i><h2>Menus</h2></a>
            <a href="/employe/plats"    class="dashboard-card"><i class="bi bi-egg-fried" aria-hidden="true"></i><h2>Plats</h2></a>
            <a href="/employe/horaires" class="dashboard-card"><i class="bi bi-clock-fill" aria-hidden="true"></i><h2>Horaires</h2></a>
            <a href="/employe/avis"     class="dashboard-card"><i class="bi bi-star-fill" aria-hidden="true"></i><h2>Avis (<?= $nbAvisAttente ?> en attente)</h2></a>
        </div>

        <form method="get" action="/employe/commandes" class="filtres">
            <div class="filtres-grid">
                <div class="form-field">
                    <label for="statut">Statut</label>
                    <select id="statut" name="statut">
                        <option value="">Tous</option>
                        <?php foreach (['en_attente','accepte','en_preparation','en_cours_de_livraison','livre','en_attente_retour_materiel','terminee','annulee'] as $s): ?>
                            <option value="<?= $s ?>" <?= $statut === $s ? 'selected' : '' ?>><?= e(format_statut($s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="q">Rechercher (client, n° commande)</label>
                    <input type="text" id="q" name="q" value="<?= e((string) $recherche) ?>">
                </div>
                <div class="form-field form-field-actions">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </div>
        </form>

        <div class="table-wrapper mt-4">
            <table class="table">
                <thead>
                    <tr>
                        <th>N°</th><th>Client</th><th>Menu</th><th>Date</th><th>Total</th><th>Statut</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $c): ?>
                        <tr>
                            <td><?= e($c['numero_commande']) ?></td>
                            <td><?= e($c['prenom']) ?> <?= e($c['nom']) ?><br><small><?= e($c['email']) ?></small></td>
                            <td><?= e($c['menu_titre']) ?> (×<?= (int) $c['nombre_personne'] ?>)</td>
                            <td><?= e(format_date_fr($c['date_prestation'])) ?></td>
                            <td><?= format_prix((float) $c['prix_total']) ?></td>
                            <td><span class="badge"><?= e(format_statut($c['statut'])) ?></span></td>
                            <td>
                                <form method="post" action="/employe/commandes/<?= urlencode($c['numero_commande']) ?>/statut" class="inline-form">
                                    <?= csrf_field() ?>
                                    <select name="statut" required>
                                        <option value="">→ Changer statut</option>
                                        <option value="accepte">Accepter</option>
                                        <option value="en_preparation">En préparation</option>
                                        <option value="en_cours_de_livraison">En livraison</option>
                                        <option value="livre">Livré</option>
                                        <option value="en_attente_retour_materiel">Attente retour matériel</option>
                                        <option value="terminee">Terminer</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">OK</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
