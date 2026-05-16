# Maquettes — Vite & Gourmand

Maquettes **haute fidélité** générées via Puppeteer (script `tools/screenshots.cjs`).
Toutes les pages sont capturées en **desktop (1440×900)** et **mobile (414×896)**.

## 1. Page d'accueil

### Desktop
![Accueil — desktop](img/01-accueil-desktop.png)

### Mobile
![Accueil — mobile](img/01-accueil-mobile.png)

---

## 2. Liste des menus avec filtres

### Desktop
![Liste menus — desktop](img/02-liste-menus-desktop.png)

### Mobile
![Liste menus — mobile](img/02-liste-menus-mobile.png)

---

## 3. Détail d'un menu

### Desktop
![Détail menu — desktop](img/03-detail-menu-desktop.png)

### Mobile
![Détail menu — mobile](img/03-detail-menu-mobile.png)

---

## 4. Page de connexion

### Desktop
![Connexion — desktop](img/04-connexion-desktop.png)

### Mobile
![Connexion — mobile](img/04-connexion-mobile.png)

---

## 5. Création de compte

### Desktop
![Inscription — desktop](img/05-inscription-desktop.png)

### Mobile
![Inscription — mobile](img/05-inscription-mobile.png)

---

## 6. Espace employé

### Desktop
![Employé dashboard — desktop](img/06-employe-dashboard-desktop.png)

### Mobile
![Employé dashboard — mobile](img/06-employe-dashboard-mobile.png)

---

## Reproduire ces captures

```bash
# Démarrer le serveur PHP local
php -S localhost:8001 -t public

# Dans un autre terminal
node tools/screenshots.cjs http://localhost:8001
```

Les captures sont régénérées dans `docs/maquettes/img/`.
