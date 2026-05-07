<?php /** @var array $stats */ ?>
<section class="section">
    <div class="container">
        <h1>Statistiques — Commandes par menu</h1>
        <p class="text-muted">Source : base de données non-relationnelle (MongoDB)</p>

        <div class="chart-container">
            <canvas id="chart-commandes" aria-label="Graphique des commandes par menu" role="img"></canvas>
        </div>

        <h2 class="mt-5">Détail</h2>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Menu</th><th>Nb commandes</th><th>CA total</th></tr></thead>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>window.STATS = <?= json_encode($stats, JSON_UNESCAPED_UNICODE) ?>;</script>
<script src="/js/admin-charts.js"></script>
