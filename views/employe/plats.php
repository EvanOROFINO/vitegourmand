<?php /** @var array $plats */ ?>
<section class="section">
    <div class="container">
        <h1>Gérer les plats</h1>

        <form method="post" action="/employe/plats" class="form filtres">
            <?= csrf_field() ?>
            <div class="filtres-grid">
                <div class="form-field">
                    <label for="titre">Nouveau plat</label>
                    <input type="text" id="titre" name="titre" required>
                </div>
                <div class="form-field">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="entree">Entrée</option>
                        <option value="plat">Plat</option>
                        <option value="dessert">Dessert</option>
                    </select>
                </div>
                <div class="form-field form-field-actions">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </form>

        <div class="table-wrapper mt-4">
            <table class="table">
                <thead><tr><th>Titre</th><th>Type</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($plats as $p): ?>
                        <tr>
                            <td><?= e($p['titre']) ?></td>
                            <td><?= e(ucfirst($p['type'])) ?></td>
                            <td>
                                <form method="post" action="/employe/plats/<?= (int) $p['plat_id'] ?>/supprimer" class="inline-form" onsubmit="return confirm('Supprimer ce plat ?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
