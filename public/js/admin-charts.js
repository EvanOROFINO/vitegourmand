/**
 * Vite & Gourmand — Graphiques de l'espace administrateur (Chart.js).
 * Source : window.STATS injecté par la vue (données venant de MongoDB).
 */
(() => {
    'use strict';

    const stats = window.STATS || [];
    const canvas = document.getElementById('chart-commandes');
    if (!canvas || !stats.length) return;

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: stats.map(s => s.titre),
            datasets: [
                {
                    label: 'Nombre de commandes',
                    data: stats.map(s => s.nb_commandes),
                    backgroundColor: '#C8552B',
                    borderRadius: 6,
                    yAxisID: 'y',
                },
                {
                    label: 'CA (€)',
                    data: stats.map(s => s.ca_total),
                    backgroundColor: '#6E8B5C',
                    borderRadius: 6,
                    yAxisID: 'y2',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                title:  { display: true, text: 'Commandes et chiffre d\'affaires par menu' },
            },
            scales: {
                y:  { beginAtZero: true, position: 'left',  title: { display: true, text: 'Nb commandes' } },
                y2: { beginAtZero: true, position: 'right', title: { display: true, text: 'CA (€)' }, grid: { drawOnChartArea: false } },
            },
        },
    });
})();
