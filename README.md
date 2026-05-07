# Vite & Gourmand

Application web pour le traiteur fictif **Vite & Gourmand** (Julie & José, Bordeaux). Permet la consultation des menus, la commande en ligne et la gestion des prestations par les employés et l'administrateur.

Projet réalisé dans le cadre de l'**ECF 2 du TP Développeur Web et Web Mobile (Studi)**.

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Front-end | HTML5, CSS3 (custom), JavaScript vanilla, Bootstrap Icons, Chart.js |
| Back-end | PHP 8.2, PDO, architecture MVC maison (autoload PSR-4 via Composer) |
| BDD relationnelle | MySQL / MariaDB |
| BDD NoSQL | MongoDB (statistiques de commandes par menu) |
| Mailer | PHPMailer |
| Déploiement | Railway / fly.io / Vercel (au choix) |

---

## Installation locale

### 1. Pré-requis

- PHP 8.2+ (avec extensions `pdo_mysql`, `mongodb`, `mbstring`)
- Composer
- MySQL / MariaDB (XAMPP, WAMP ou MySQL Server natif)
- MongoDB Community Server (port 27017)

### 2. Cloner le dépôt

```bash
git clone https://github.com/EvanOROFINO/vitegourmand.git
cd vitegourmand
```

### 3. Installer les dépendances

```bash
composer install
```

### 4. Configurer l'environnement

```bash
cp .env.example .env
```

Éditer `.env` et renseigner les identifiants MySQL.

### 5. Créer la base de données

```bash
mysql -u root -p < sql/01_schema.sql
mysql -u root -p < sql/02_seed.sql
```

### 6. Démarrer MongoDB

```bash
# Windows
& "C:\Program Files\MongoDB\Server\8.2\bin\mongod.exe" --dbpath "C:\data\db" --port 27017 --bind_ip 127.0.0.1

# Linux/Mac
mongod --dbpath /data/db
```

### 7. Lancer le serveur PHP

```bash
php -S localhost:8000 -t public
```

Ouvrir [http://localhost:8000](http://localhost:8000).

---

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@vitegourmand.fr | `Password123!` |
| Employé | employe@vitegourmand.fr | `Password123!` |
| Utilisateur | user@vitegourmand.fr | `Password123!` |

---

## Stratégie Git

- `main` : branche de production (code stable et déployé)
- `develop` : branche de développement (toutes les features y sont mergées avant tests)
- `feature/<nom-feature>` : une branche par fonctionnalité, mergée dans `develop` après tests

---

## Structure du projet

```
vitegourmand/
├── config/             Configuration (chargement .env)
├── public/             Point d'entrée web (index.php, .htaccess, css/, js/, uploads/)
├── src/
│   ├── Core/           Noyau MVC (Router, Controller, Database, Mongo, Auth, Security)
│   ├── Controllers/    Contrôleurs
│   ├── Models/         Modèles d'accès aux données
│   └── Helpers/        Fonctions utilitaires
├── views/              Templates PHP (layouts, partials, pages)
├── sql/                Scripts SQL (schéma + seed)
├── docs/               Documentation (charte, technique, gestion projet)
└── routes.php          Définition des routes HTTP
```

---

## Auteur

**Evan OROFINO** — Étudiant DWWM Studi 2025-2026
