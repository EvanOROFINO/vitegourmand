/**
 * Vite & Gourmand — JavaScript de l'application.
 */

(() => {
    'use strict';

    // ===== Menu mobile : toggle =====
    const navToggle = document.querySelector('.navbar-toggle');
    const navList   = document.getElementById('nav-list');
    if (navToggle && navList) {
        navToggle.addEventListener('click', () => {
            const open = navList.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // ===== Auto-dismiss des alerts après 6 s =====
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .4s, transform .4s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 400);
        }, 6000);
    });

    // ===== Filtres dynamiques sur la liste des menus (sans rechargement) =====
    const filtresForm = document.getElementById('filtres-form');
    const grid        = document.getElementById('menus-grid');
    if (filtresForm && grid) {
        let timer;

        const fetchMenus = async () => {
            const params = new URLSearchParams();
            ['prix_min', 'prix_max', 'theme', 'regime', 'personnes'].forEach(k => {
                const v = filtresForm.elements[k]?.value;
                if (v) params.append(k, v);
            });

            try {
                const res = await fetch('/api/menus?' + params.toString());
                if (!res.ok) throw new Error('Erreur serveur');
                const menus = await res.json();
                grid.innerHTML = renderMenus(menus);
            } catch (e) {
                console.error(e);
            }
        };

        const renderMenus = (menus) => {
            if (!menus.length) {
                return `<p class="text-muted text-center" style="grid-column: 1 / -1;">Aucun menu ne correspond à vos critères.</p>`;
            }
            return menus.map(m => `
                <article class="menu-card">
                    ${m.image_principale ? `<img src="${escapeHtml(m.image_principale)}" alt="" class="menu-card-image" loading="lazy">` : ''}
                    <div class="menu-card-body">
                        <div class="menu-card-tags">
                            ${m.theme_libelle ? `<span class="badge">${escapeHtml(m.theme_libelle)}</span>` : ''}
                            ${m.regime_libelle ? `<span class="badge badge-secondary">${escapeHtml(m.regime_libelle)}</span>` : ''}
                        </div>
                        <h3 class="menu-card-title">${escapeHtml(m.titre)}</h3>
                        <p class="menu-card-desc">${escapeHtml((m.description || '').slice(0, 120))}…</p>
                        <div class="menu-card-meta">
                            <span><i class="bi bi-people-fill"></i> ${m.nombre_personne_minimum} pers. min.</span>
                            <span class="menu-card-prix">${formatPrix(m.prix_par_personne)} / pers.</span>
                        </div>
                        <a href="/menus/${m.menu_id}" class="btn btn-primary btn-block">Voir le détail</a>
                    </div>
                </article>
            `).join('');
        };

        const escapeHtml = (str) => String(str ?? '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');

        const formatPrix = (n) => Number(n).toFixed(2).replace('.', ',') + ' €';

        filtresForm.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(fetchMenus, 300);
        });
        filtresForm.addEventListener('reset', () => {
            setTimeout(fetchMenus, 50);
        });
    }

    // ===== Calcul prix dynamique sur le formulaire de commande =====
    const commandeForm = document.getElementById('commande-form');
    if (commandeForm) {
        const recap = document.getElementById('recapitulatif');
        const prixPersonne = parseFloat(recap.dataset.prixPersonne);
        const minPersonnes = parseInt(recap.dataset.min, 10);

        const inputNb       = document.getElementById('nombre_personne');
        const inputVille    = document.getElementById('ville_livraison');
        const inputDistance = document.getElementById('distance_km');

        const calculer = () => {
            const nb       = Math.max(parseInt(inputNb.value, 10) || 0, minPersonnes);
            const ville    = (inputVille.value || '').trim().toLowerCase();
            const distance = parseFloat(inputDistance.value) || 0;

            const prixMenu = nb * prixPersonne;

            let reduc = 0;
            if (nb >= minPersonnes + 5) reduc = prixMenu * 0.10;

            let livr = 0;
            if (ville && ville !== 'bordeaux' && distance > 0) livr = 5 + 0.59 * distance;

            const total = prixMenu - reduc + livr;

            const fmt = (n) => n.toFixed(2).replace('.', ',') + ' €';
            document.getElementById('r-nb').textContent     = nb;
            document.getElementById('r-menu').textContent   = fmt(prixMenu);
            document.getElementById('r-reduc').textContent  = '−' + fmt(reduc);
            document.getElementById('r-livr').textContent   = fmt(livr);
            document.getElementById('r-total').textContent  = fmt(total);

            document.getElementById('r-reduc-line').hidden = reduc <= 0;
            document.getElementById('r-livr-line').hidden  = livr  <= 0;
        };

        [inputNb, inputVille, inputDistance].forEach(el => {
            el.addEventListener('input', calculer);
        });
        calculer();
    }
})();
