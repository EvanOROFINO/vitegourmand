# Guide de déploiement Render — Vite & Gourmand

Ce guide accompagne le déploiement sur **Render** (PHP via Docker) avec **MongoDB Atlas** (NoSQL) et une **MySQL distante** (PHPMyAdmin / Aiven / db4free).

## Architecture cible

```
┌─────────────────────────────┐       ┌──────────────────┐
│  Navigateur                 │──────▶│ Render (Docker)  │
└─────────────────────────────┘       │ vitegourmand-php │
                                       └────────┬─────────┘
                                                │
                                ┌───────────────┼────────────────┐
                                ▼                                ▼
                       ┌──────────────────┐           ┌──────────────────┐
                       │ MySQL distante   │           │ MongoDB Atlas    │
                       │ (db4free / Aiven)│           │ (cluster M0)     │
                       └──────────────────┘           └──────────────────┘
```

## 1. Préparer la BDD MySQL distante

### Option A : db4free.net (le plus simple, 100 % gratuit)

1. Aller sur https://www.db4free.net
2. Cliquer sur **"Subscribe a new database"**
3. Renseigner :
   - Database name : `vitegourmand`
   - Username : `vitegourmand_evan` (libre)
   - Password : choisir un mot de passe fort (le noter !)
   - E-mail : ton e-mail
4. Confirmer via le mail reçu
5. Importer le schéma : se connecter sur https://www.db4free.net/phpMyAdmin/, sélectionner la BDD, **Importer** → uploader `sql/01_schema.sql` puis `sql/02_seed.sql`

→ Noter ces variables :
- `DB_HOST = db4free.net`
- `DB_PORT = 3306`
- `DB_NAME = vitegourmand`
- `DB_USER = vitegourmand_evan`
- `DB_PASSWORD = <le mot de passe>`

## 2. Préparer MongoDB Atlas

1. Aller sur https://cloud.mongodb.com (ton compte est déjà créé)
2. Créer un cluster gratuit **M0** si ce n'est pas déjà fait
3. **Database Access** → "Add new database user" :
   - Username : `vitegourmand`
   - Password : générer ou choisir
   - Built-in role : **Read and write to any database**
4. **Network Access** → "Add IP address" → **Allow access from anywhere** (0.0.0.0/0)
5. **Database** → "Connect" → "Drivers" → copier l'URI :
   `mongodb+srv://vitegourmand:<password>@cluster0.xxxxx.mongodb.net/?retryWrites=true&w=majority`
6. Remplacer `<password>` par le mot de passe défini

→ Noter `MONGO_URI = mongodb+srv://...`

## 3. Déployer sur Render

### Méthode automatique (Blueprint via render.yaml)

1. Aller sur https://render.com → **New** → **Blueprint**
2. Connecter le compte GitHub si ce n'est pas fait
3. Sélectionner le repo `EvanOROFINO/vitegourmand`
4. Render détecte le `render.yaml` → cliquer **Apply**
5. Compléter les variables d'env demandées (celles avec `sync: false`) :
   - `APP_URL` : sera donné après le 1er déploiement, pour l'instant mettre `https://vitegourmand.onrender.com`
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` : tes valeurs db4free
   - `MONGO_URI` : ton URI Atlas
   - `MAIL_USERNAME`, `MAIL_PASSWORD` : laisser vide en dev (les mails seront loggés dans `/var/mail-debug`)
6. Cliquer **Create Web Service**
7. Attendre la fin du build (~5 minutes la 1re fois)

### Méthode manuelle (sans render.yaml)

Si Blueprint ne marche pas :

1. **New** → **Web Service** → connecter le repo `vitegourmand`
2. Configuration :
   - **Name** : `vitegourmand`
   - **Region** : Frankfurt (Europe)
   - **Branch** : `main`
   - **Runtime** : **Docker**
   - **Plan** : **Free**
3. Ajouter chaque variable d'environnement manuellement (voir liste plus haut)
4. Créer le service

## 4. Vérification post-déploiement

Après le déploiement :

1. Récupérer l'URL publique (ex : `https://vitegourmand.onrender.com`)
2. Mettre à jour la variable `APP_URL` dans Render avec cette URL
3. Tester :
   - Page d'accueil : `https://vitegourmand.onrender.com/`
   - Login admin : `admin@vitegourmand.fr` / `Password123!`
   - Liste des menus : `/menus`
   - Espace admin : `/admin`

## 5. Limitations du plan Free

- Le service **s'endort après 15 minutes d'inactivité** → première requête après réveil = 30 secondes de latence (le jury sera prévenu, c'est normal)
- 750 h de service / mois gratuit
- 100 Go de bande passante / mois

## 6. Dépannage

### `Error: Could not connect to MySQL`
- Vérifier que `DB_HOST=db4free.net` et `DB_PORT=3306`
- Vérifier que la BDD existe et que l'utilisateur a bien les droits

### `Error: Connection to MongoDB failed`
- Vérifier que **Network Access** Atlas autorise `0.0.0.0/0`
- Vérifier que le mot de passe dans l'URI ne contient pas de caractère spécial non-encodé

### Build échoue
- Voir les logs dans Render → service → **Logs**
- Souvent : extension PHP manquante → ajuster le Dockerfile
