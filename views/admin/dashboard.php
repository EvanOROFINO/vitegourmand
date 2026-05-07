<?php /** @var array $stats */ ?>
<section class="section">
    <div class="container">
        <h1>Espace administrateur</h1>
        <p>Bienvenue dans votre tableau de bord.</p>

        <div class="grid grid-3 mt-4">
            <a href="/admin/employes"        class="dashboard-card"><i class="bi bi-people-fill" aria-hidden="true"></i><h2>Employés</h2><p>Créer, désactiver les comptes</p></a>
            <a href="/admin/statistiques"    class="dashboard-card"><i class="bi bi-bar-chart-fill" aria-hidden="true"></i><h2>Statistiques</h2><p>Commandes par menu</p></a>
            <a href="/admin/chiffre-affaires" class="dashboard-card"><i class="bi bi-currency-euro" aria-hidden="true"></i><h2>Chiffre d'affaires</h2><p>Par menu et période</p></a>
            <a href="/employe"               class="dashboard-card"><i class="bi bi-bag-fill" aria-hidden="true"></i><h2>Commandes</h2><p>Gérer les commandes</p></a>
            <a href="/employe/menus"         class="dashboard-card"><i class="bi bi-cup-hot-fill" aria-hidden="true"></i><h2>Menus</h2></a>
            <a href="/employe/avis"          class="dashboard-card"><i class="bi bi-star-fill" aria-hidden="true"></i><h2>Avis</h2></a>
        </div>

        <h2 class="mt-5">Aperçu : commandes par menu</h2>
        <p class="text-muted">Données issues de la base NoSQL (MongoDB)</p>
        <div class="chart-container">
            <canvas id="chart-commandes" aria-label="Graphique des commandes par menu" role="img"></canvas>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.STATS = <?= json_encode($stats, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/js/admin-charts.js"></script>
