<?php /** @var array $employes */ ?>
<section class="section">
    <div class="container">
        <h1>Gérer les employés</h1>

        <h2>Créer un nouveau compte employé</h2>
        <form method="post" action="/admin/employes" class="form filtres">
            <?= csrf_field() ?>
            <div class="filtres-grid">
                <div class="form-field">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-field">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-field">
                    <label for="email">E-mail (username) <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-field">
                    <label for="password">Mot de passe initial <span class="required">*</span></label>
                    <input type="password" id="password" name="password" required minlength="10">
                    <small class="form-help">À transmettre en main propre à l'employé.</small>
                </div>
                <div class="form-field form-field-actions">
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </div>
        </form>

        <h2 class="mt-5">Comptes employés existants</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Prénom</th><th>Nom</th><th>Email</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($employes as $emp): ?>
                        <tr>
                            <td><?= e($emp['prenom']) ?></td>
                            <td><?= e($emp['nom']) ?></td>
                            <td><?= e($emp['email']) ?></td>
                            <td><?= $emp['actif'] ? '✅ Actif' : '❌ Désactivé' ?></td>
                            <td>
                                <?php if ($emp['actif']): ?>
                                    <form method="post" action="/admin/employes/<?= (int) $emp['utilisateur_id'] ?>/desactiver" class="inline-form" onsubmit="return confirm('Désactiver ce compte ?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger">Désactiver</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="/admin/employes/<?= (int) $emp['utilisateur_id'] ?>/reactiver" class="inline-form">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-primary">Réactiver</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
