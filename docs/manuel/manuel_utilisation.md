# Manuel d'utilisation — Vite & Gourmand

Application web de prise de commande pour le traiteur Vite & Gourmand (Bordeaux).

---

## 1. Accès à l'application

- **URL en ligne** : https://vitegourmand-s4in.onrender.com
- **URL en local** : http://localhost:8000

---

## 2. Comptes de démonstration

Tous les comptes ont pour mot de passe : **`Password123!`**

| Rôle | Email | Capacités |
|------|-------|-----------|
| 👑 Administrateur | `admin@vitegourmand.fr` | Toutes les actions de l'application |
| 👨‍🍳 Employé | `employe@vitegourmand.fr` | Gestion menus / commandes / avis / horaires |
| 👤 Utilisateur | `user@vitegourmand.fr` | Consulter, commander, donner un avis |
| 👤 Utilisateur 2 | `jean@example.fr` | Idem ci-dessus |

---

## 3. Parcours visiteur (sans compte)

### 3.1 Découvrir l'entreprise

Sur la page d'accueil, le visiteur découvre :
- Une présentation de Julie & José
- Trois atouts (25 ans d'expérience, produits locaux, sur-mesure)
- Les avis clients **validés** par un employé

### 3.2 Consulter les menus

Cliquer sur **« Nos menus »** dans la navbar.
- 6 menus sont proposés par défaut (Tradition Bordelaise, Noël Festif, Végétarien Gourmand, Pâques Familial, Été Frais, Évènement Prestige).
- Des **filtres dynamiques** sont disponibles (sans rechargement de page) :
  - Prix minimum / maximum (€/personne)
  - Thème (Classique, Noël, Pâques, Évènement, Été)
  - Régime (Classique, Végétarien, Végan, Sans gluten, Halal)
  - Nombre de personnes minimum

### 3.3 Voir le détail d'un menu

En cliquant sur **« Voir le détail »** :
- Galerie d'images
- Description complète
- Composition (entrée / plat / dessert avec allergènes)
- Liste agrégée des allergènes du menu
- **Conditions du menu** (mises en avant en bandeau jaune)
- Prix par personne, nombre minimum, stock restant
- Bouton **« Commander »** (redirige vers connexion si visiteur non authentifié)

### 3.4 Contacter l'entreprise

Cliquer sur **« Contact »** dans la navbar :
- Formulaire avec titre, e-mail, message
- Une fois envoyé : confirmation à l'écran + e-mail à l'équipe Vite & Gourmand

### 3.5 Mentions légales / CGV

Accessible depuis le footer.

---

## 4. Parcours utilisateur (compte client)

### 4.1 Créer un compte

Cliquer sur **« Connexion »** puis **« Créez-en un »**.

Champs requis :
- Prénom, nom
- E-mail (servira d'identifiant)
- Téléphone (GSM)
- Adresse postale, ville, pays
- Mot de passe sécurisé : **10 caractères minimum, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial**

→ Un mail de bienvenue automatique est envoyé.

### 4.2 Se connecter

Email + mot de passe. En cas d'oubli, lien **« Mot de passe oublié »** qui envoie un mail avec un lien de réinitialisation valable 1 heure.

### 4.3 Passer une commande

1. Sur la page détail d'un menu, cliquer sur **« Commander »**
2. Le formulaire est pré-rempli avec les informations du compte
3. Renseigner :
   - Date et heure de la prestation (J+1 minimum)
   - Adresse de livraison + ville
   - Distance depuis Bordeaux en km (si livraison hors Bordeaux)
   - Nombre de personnes (minimum imposé par le menu)
4. Le **récapitulatif tarifaire** se met à jour en temps réel :
   - Prix menu = `nombre × prix_par_personne`
   - **Réduction −10 %** automatique si `nombre_personnes ≥ minimum + 5`
   - **Frais livraison = 5 € + 0,59 €/km** si livraison hors Bordeaux
5. Valider → numéro de commande unique généré + mail de confirmation

### 4.4 Suivre ses commandes

Espace **« Mon espace » → « Mes commandes »**.

Pour chaque commande, accès au détail :
- Informations
- Tarification
- **Suivi temps réel** des changements de statut (timeline)

#### Statuts possibles

```
en_attente → accepte → en_preparation → en_cours_de_livraison → livre → terminee
                ↓
            (avec prêt matériel)
                ↓
        en_attente_retour_materiel → terminee
```

### 4.5 Modifier ou annuler une commande

**Tant que le statut est `en_attente`** (non traitée par un employé) :
- Modification possible (sauf le menu choisi)
- Annulation possible

**Une fois le statut `accepte` ou plus** : il faut contacter l'équipe.

### 4.6 Donner un avis

Une fois la commande **`terminee`**, un mail invite à laisser un avis.

Depuis le détail de la commande, cliquer sur **« Donner mon avis »** :
- Note de 1 à 5 étoiles
- Commentaire libre

L'avis est créé en `en_attente` de modération. Une fois validé par un employé, il s'affiche sur la page d'accueil.

---

## 5. Parcours employé

### 5.1 Tableau de bord

URL : `/employe`. Vue principale = **liste des commandes** avec :
- Filtre par statut
- Recherche client (nom, prénom, e-mail, n° commande)
- Action **« Changer statut »** par dropdown

### 5.2 Gérer les commandes

L'employé peut faire évoluer une commande dans n'importe quel statut. Mails automatiques envoyés à certains paliers :
- `en_attente_retour_materiel` (si prêt matériel) → mail avec mention 600 € si non rendu sous 10 jours ouvrés
- `terminee` → mail invitant à donner un avis

**Annulation** : possible mais nécessite un **motif** + **mode de contact** (GSM ou mail).

### 5.3 Gérer les menus

URL : `/employe/menus`.
- Création / édition / désactivation
- Choix du thème, régime, nombre de personnes minimum, prix, conditions, stock
- Sélection des plats inclus (case à cocher pour chaque plat)

### 5.4 Gérer les plats

URL : `/employe/plats`.
- Ajout d'un plat (titre + type entrée/plat/dessert)
- Suppression

### 5.5 Gérer les horaires

URL : `/employe/horaires`. Modification des heures d'ouverture/fermeture pour chaque jour de la semaine.

### 5.6 Modérer les avis

URL : `/employe/avis`. Liste des avis en attente de validation.

Pour chaque avis : aperçu (note + commentaire) + boutons **Valider** / **Refuser**.

---

## 6. Parcours administrateur

L'admin a tous les droits d'un employé **plus** :

### 6.1 Gérer les comptes employés

URL : `/admin/employes`.

**Création d'un employé** : prénom, nom, e-mail, mot de passe initial.
- Le mot de passe **n'est PAS envoyé par e-mail** (à transmettre en main propre, conformément à l'énoncé)
- Un mail de bienvenue avertit l'employé de l'existence de son compte

**Désactivation / réactivation** d'un compte employé.

> ⚠ Conformément à l'énoncé : **aucun compte administrateur ne peut être créé depuis l'application**. L'admin initial est créé manuellement en BDD via le seed SQL.

### 6.2 Statistiques (BDD NoSQL)

URL : `/admin/statistiques`.

Graphique en barres (Chart.js) montrant le **nombre de commandes par menu** et le **chiffre d'affaires** correspondant. Ces données viennent de la base **MongoDB** (collection `commandes_par_menu`).

### 6.3 Chiffre d'affaires

URL : `/admin/chiffre-affaires`.

Filtres :
- Période (date début / date fin)
- Menu spécifique

KPIs : nombre total de commandes + CA total sur la période. Détail par menu en tableau.

---

## 7. Sécurité et conformité

| Mesure | Implémentation |
|--------|----------------|
| Hachage mot de passe | bcrypt (PHP `password_hash`, coût 10) |
| Protection CSRF | Jeton unique par session, vérifié sur chaque POST |
| Protection XSS | Échappement HTML systématique via `e()` (htmlspecialchars) |
| Protection SQL injection | Requêtes préparées PDO avec paramètres nommés |
| Sessions | `cookie_httponly`, `cookie_samesite=Lax`, `use_strict_mode`, regenerate sur login |
| RBAC | Rôles `utilisateur`, `employe`, `administrateur` vérifiés par `Auth::requireRole()` |
| Headers HTTP | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Content-Security-Policy` |
| RGPD | Mentions légales + droit d'accès/rectification/suppression mentionné |
| RGAA | Skip-link, aria-labels, contrastes WCAG AA, navigation clavier |

---

## 8. Dépannage

### MySQL ne démarre pas
```powershell
Start-Process "C:\xampp\mysql\bin\mysqld.exe" -ArgumentList "--defaults-file=C:\xampp\mysql\bin\my.ini"
```

### MongoDB ne démarre pas
```powershell
& "C:\Program Files\MongoDB\Server\8.2\bin\mongod.exe" --dbpath "C:\data\db" --port 27017
```

### Réinitialiser la BDD
```powershell
Get-Content sql/01_schema.sql | & "C:\xampp\mysql\bin\mysql.exe" -u root
Get-Content sql/02_seed.sql   | & "C:\xampp\mysql\bin\mysql.exe" -u root
```

### Voir les mails envoyés en mode dev
Les mails sont écrits dans `var/mail-debug/` (un fichier HTML par mail).
