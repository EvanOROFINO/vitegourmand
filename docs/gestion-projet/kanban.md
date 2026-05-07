# Tableau Kanban — Vite & Gourmand

État au **2026-05-07** (livraison du projet).

## ✅ Fait (14 / 14 user stories implémentées)

### US01 · Page d'accueil
- [x] Hero avec photo et CTA
- [x] Présentation 3 atouts (expérience, produits locaux, sur-mesure)
- [x] Section avis clients (uniquement validés)

### US02 · Liste des menus + filtres dynamiques
- [x] Grid responsive (3 cols desktop, 1 col mobile)
- [x] 5 filtres : prix min, prix max, thème, régime, nb personnes
- [x] Actualisation **sans rechargement de page** (fetch + injection HTML)

### US03 · Détail d'un menu
- [x] Galerie d'images
- [x] Composition par catégorie (entrée / plat / dessert)
- [x] Liste agrégée des allergènes
- [x] Conditions du menu **mises en évidence**
- [x] Bouton commande (ou redirection login si visiteur)

### US04 · Création de compte
- [x] Formulaire complet (nom, prénom, email, GSM, adresse, ville, pays)
- [x] Validation mot de passe **10 car. min, 1 maj, 1 min, 1 chiffre, 1 spécial**
- [x] Mail de bienvenue automatique
- [x] Rôle `utilisateur` attribué automatiquement

### US05 · Connexion / mot de passe oublié
- [x] Login avec email + mot de passe
- [x] Lien "Mot de passe oublié" → mail avec token (1h de validité)
- [x] Page de réinitialisation

### US06 · Tunnel de commande
- [x] Pré-remplissage avec infos du compte
- [x] Date de prestation J+1 minimum
- [x] Calcul prix temps réel (côté client + côté serveur)
- [x] **Réduction 10%** automatique si nb_pers ≥ minimum + 5
- [x] **Frais livraison 5€ + 0,59€/km** hors Bordeaux
- [x] Numéro de commande unique format `CMD-YYYYMMDD-XXXXXX`
- [x] Mail de confirmation
- [x] Stock décrémenté

### US07 · Suivi / modification / annulation
- [x] Liste des commandes utilisateur
- [x] Détail avec **timeline des statuts**
- [x] Annulation possible si statut `en_attente`
- [x] Modification possible si statut `en_attente` (sauf le menu choisi)

### US08 · Système d'avis
- [x] Note 1-5 étoiles + commentaire
- [x] Disponible uniquement après statut `terminee`
- [x] **Modération** par employé avant publication
- [x] Avis validés visibles sur la page d'accueil

### US09 · Gestion commandes (employé)
- [x] Dashboard avec liste filtrée
- [x] Filtre par statut + recherche client/n° commande
- [x] Changement de statut avec workflow complet
- [x] Annulation avec **motif** + **mode de contact** obligatoires

### US10 · CRUD menus / plats / horaires + modération avis (employé)
- [x] CRUD complet sur les menus (titre, descr, prix, conditions, stock, plats inclus)
- [x] CRUD plats (entrée / plat / dessert)
- [x] Édition des horaires d'ouverture
- [x] Validation / refus des avis en attente

### US11 · Gestion employés (admin)
- [x] Création employé avec mdp défini par l'admin
- [x] Mail de notification (sans le mot de passe en clair, comme exigé par l'énoncé)
- [x] Désactivation / réactivation
- [x] **Pas de création admin depuis l'app** (conformité au sujet)

### US12 · Statistiques NoSQL (admin)
- [x] Collection MongoDB `commandes_par_menu` mise à jour à chaque commande
- [x] Graphique en barres avec **Chart.js**
- [x] Données issues exclusivement de la BDD non-relationnelle (exigence sujet)

### US13 · CA par menu et par période (admin)
- [x] Filtres date début / date fin / menu
- [x] KPIs en haut (total commandes, total CA)
- [x] Tableau détaillé par menu

### US14 · Formulaire de contact
- [x] Formulaire titre + email + message
- [x] Stockage en BDD pour traçabilité
- [x] Mail à l'équipe Vite & Gourmand

## ✅ Conformité

- [x] RGPD : mentions légales, droit d'accès / rectification / suppression mentionné
- [x] RGAA : skip-link, aria-labels, contrastes WCAG AA, navigation clavier
- [x] Sécurité : OWASP top 10 audité (CSRF, XSS, SQLi, sessions, etc.)
- [x] Mots de passe : politique stricte (10 car., 4 classes)
- [x] Comptes admin : non créables depuis l'app

## ✅ Livrables

- [x] Code sur GitHub PUBLIC
- [x] README avec instructions complètes
- [x] Fichiers SQL (`01_schema.sql` + `02_seed.sql`)
- [x] Charte graphique PDF (palette, typo, maquettes)
- [x] Manuel d'utilisation PDF
- [x] Documentation technique PDF (MCD, diagrammes, déploiement)
- [x] Documentation gestion de projet PDF (méthode, kanban)
- [x] Application déployée en ligne *(à compléter avec l'URL)*
- [x] Branches Git `main` + `develop` + `feature/*`

## 🚧 À faire (post-livraison, hors scope ECF)

- [ ] Tests automatisés (PHPUnit)
- [ ] Internationalisation FR / EN
- [ ] Panier multi-menus
- [ ] Paiement en ligne (Stripe)
- [ ] Mode hors-ligne (PWA)
- [ ] Accès employé à un calendrier des prestations
