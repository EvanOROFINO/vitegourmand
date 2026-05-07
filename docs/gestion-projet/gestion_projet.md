# Gestion de projet — Vite & Gourmand

## 1. Méthode

J'ai opté pour une **approche agile simplifiée** inspirée de Scrum / Kanban :

- **Découpage en User Stories (US)** dérivées du sujet
- **Sprints courts** (1 semaine en moyenne) pour livrer des incréments fonctionnels
- **Tableau Kanban** pour visualiser l'avancement (`Backlog → En cours → En revue → Fait`)
- **Tests fréquents** après chaque US pour limiter la dette technique

Les outils choisis :
- **Trello** (ou Notion) pour le Kanban et le suivi
- **Git + GitHub** pour le versioning et la traçabilité (avec branches `main` / `develop` / `feature/*`)
- **VS Code** comme IDE principal

## 2. Découpage en User Stories

| # | User Story | Estimation | Priorité |
|---|------------|------------|----------|
| US01 | En tant que **visiteur**, je veux voir une page d'accueil présentant l'entreprise et les avis validés, pour me donner confiance | S | Haute |
| US02 | En tant que **visiteur**, je veux consulter la liste des menus avec filtres dynamiques (prix, thème, régime, nb pers.), pour trouver rapidement un menu adapté | M | Haute |
| US03 | En tant que **visiteur**, je veux consulter le détail d'un menu (composition, allergènes, conditions), pour me décider en connaissance de cause | M | Haute |
| US04 | En tant que **visiteur**, je veux créer un compte avec un mot de passe sécurisé, pour pouvoir commander | M | Haute |
| US05 | En tant qu'**utilisateur**, je veux me connecter et récupérer mon mot de passe en cas d'oubli, pour accéder à mon espace | S | Haute |
| US06 | En tant qu'**utilisateur authentifié**, je veux passer commande avec calcul automatique du prix (réduction, livraison), pour réserver une prestation | L | Haute |
| US07 | En tant qu'**utilisateur**, je veux suivre, modifier ou annuler mes commandes (selon le statut), pour garder le contrôle | M | Moyenne |
| US08 | En tant qu'**utilisateur**, je veux donner un avis sur une commande terminée, pour partager mon expérience | S | Moyenne |
| US09 | En tant qu'**employé**, je veux gérer les commandes (changer le statut, annuler avec motif), pour suivre les prestations | L | Haute |
| US10 | En tant qu'**employé**, je veux gérer les menus, plats, horaires et modérer les avis, pour tenir le site à jour | L | Moyenne |
| US11 | En tant qu'**administrateur**, je veux créer / désactiver des comptes employés, pour gérer mon équipe | M | Moyenne |
| US12 | En tant qu'**administrateur**, je veux visualiser les statistiques de commandes par menu (graphique) issues d'une BDD NoSQL, pour piloter l'activité | M | Moyenne |
| US13 | En tant qu'**administrateur**, je veux calculer le CA par menu et par période, pour produire mes rapports | M | Moyenne |
| US14 | En tant que **visiteur**, je veux contacter l'entreprise via un formulaire, pour obtenir des renseignements | S | Basse |

> Estimation : `S` = simple (≤ 4 h), `M` = moyen (4–8 h), `L` = long (≥ 1 jour)

## 3. Roadmap / Sprints

### Sprint 1 — Setup & fondations (semaine 1)
- Installation environnement (PHP, Composer, MySQL, MongoDB)
- Architecture MVC, routeur, autoload PSR-4
- Schéma SQL + seed
- Page d'accueil (US01)
- Mentions légales / CGV

### Sprint 2 — Cœur fonctionnel public (semaine 2)
- Liste des menus + filtres dynamiques (US02)
- Détail menu (US03)
- Création de compte + connexion (US04, US05)

### Sprint 3 — Tunnel de commande (semaine 3)
- Formulaire de commande avec calcul prix temps réel (US06)
- Espace utilisateur : suivi des commandes (US07)
- Système d'avis avec modération (US08)
- Mails automatiques (bienvenue, confirmation, terminé)

### Sprint 4 — Espaces internes (semaine 4)
- Espace employé : gestion commandes et statuts (US09)
- Gestion menus / plats / horaires (US10)
- Modération avis (US10)
- Espace admin : gestion employés (US11)
- Statistiques NoSQL avec graphique (US12)
- CA par menu et période (US13)
- Formulaire de contact (US14)

### Sprint 5 — Polish, tests, livrables (semaine 5)
- Tests manuels exhaustifs (visiteur / utilisateur / employé / admin)
- Corrections bugs + accessibilité RGAA
- Charte graphique : palette, typo, maquettes (3 desktop + 3 mobile)
- Documentation : manuel utilisateur, doc technique
- Déploiement et tests en ligne

## 4. Tableau Kanban (état final)

### ✅ Fait

- US01 — Page d'accueil
- US02 — Liste menus + filtres dynamiques
- US03 — Détail menu
- US04 — Création de compte
- US05 — Connexion / mdp oublié
- US06 — Tunnel de commande + calcul prix
- US07 — Suivi / modification / annulation commande
- US08 — Avis 1-5 étoiles avec modération
- US09 — Gestion commandes côté employé
- US10 — CRUD menus, plats, horaires + modération avis
- US11 — Gestion employés admin
- US12 — Stats par menu (MongoDB + Chart.js)
- US13 — CA par menu et période
- US14 — Formulaire de contact

### 🔄 En revue

- Tests d'accessibilité RGAA approfondis
- Optimisation des images

### 🚧 À faire (post-livraison)

- Internationalisation (FR/EN)
- Mode panier multi-menus (1 commande = plusieurs menus)
- Paiement en ligne (Stripe)

## 5. Stratégie Git

```
main      ─ ── ── ── ── ── ── ── ── ── ── ── ── ──
            \                          /
develop      ─── ── ── ── ── ── ── ── /
              \      \      \
feature/*    fea1   fea2   fea3
```

Règles :
- Branche `main` = code stable et déployé
- Branche `develop` = intégration des features avant validation
- Une branche par feature (`feature/listing-menus`, `feature/tunnel-commande`, etc.)
- Merge dans `develop` après revue + tests
- Merge dans `main` quand `develop` est validé sur tous les flux

## 6. Risques identifiés et mitigations

| Risque | Impact | Mitigation |
|--------|--------|------------|
| Crash du serveur MySQL (CHECK CONSTRAINT non supporté MariaDB 10.4) | Bloquant | Validation déplacée côté PHP + type `TINYINT` simple |
| Perte de données utilisateur | Critique | Backups SQL via `mysqldump` planifiés |
| Faille XSS via avis | Sécurité | Échappement HTML systématique (`htmlspecialchars`) |
| Faille CSRF sur formulaires | Sécurité | Jeton CSRF unique par session vérifié sur chaque POST |
| Faille SQL injection | Sécurité | Requêtes préparées PDO partout (audit code OK) |
| Mots de passe trop faibles | Sécurité | Politique stricte 10 car. + 4 classes |
| Mail jamais reçu (filtré spam) | UX | Logs en dev + SMTP authentifié en prod |
| Indisponibilité MongoDB | Fonctionnel (admin) | Fallback : afficher un message "stats indisponibles" |

## 7. Bilan personnel

Ce TP m'a permis de mettre en pratique l'ensemble des compétences du référentiel DWWM :

| Compétence référentiel | Mise en œuvre dans le projet |
|------------------------|-------------------------------|
| Installer / configurer son environnement | XAMPP + MongoDB + Composer + Git + VS Code |
| Maquetter des interfaces | Charte graphique + maquettes desktop/mobile |
| Réaliser des interfaces statiques | HTML5 sémantique + CSS3 (custom) |
| Développer la partie dynamique | JS vanilla (filtres dynamiques, calcul prix) + Chart.js |
| Mettre en place une BDD relationnelle | Schéma normalisé en 3FN, contraintes FK, index |
| Composants d'accès SQL et NoSQL | PDO + driver MongoDB |
| Composants métier serveur | Modèles + contrôleurs avec règles métier (calcul prix, statuts) |
| Documenter le déploiement | Doc technique complète + guide d'installation README |

Le plus gros défi a été de **concilier la richesse fonctionnelle du sujet** (3 rôles distincts, 5 statuts de commande, règles tarifaires complexes, conformité RGAA + RGPD, exigence NoSQL) avec **la maîtrise totale du code** (sans framework). L'architecture MVC maison, héritée du projet EcoRide, m'a fait gagner un temps précieux.
