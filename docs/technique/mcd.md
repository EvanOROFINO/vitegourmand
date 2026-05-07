# MCD — Vite & Gourmand

Modèle conceptuel des données — base relationnelle MariaDB / MySQL.

## Diagramme entité-relation (textuel)

```
┌─────────────────────────────┐
│ utilisateur                 │
├─────────────────────────────┤
│ utilisateur_id PK           │
│ email UQ                    │
│ password (bcrypt)           │
│ prenom / nom                │
│ telephone                   │
│ adresse_postale / ville     │
│ pays                        │
│ actif (BOOL)                │
│ reset_token / reset_expires │
│ created_at                  │
└──────────┬──────────────────┘
           │ N,N
           │
           ▼
┌─────────────────────────────┐         ┌─────────────────┐
│ possede (assoc.)            │────N,1─▶│ role            │
├─────────────────────────────┤         ├─────────────────┤
│ utilisateur_id FK           │         │ role_id PK      │
│ role_id FK                  │         │ libelle         │
└─────────────────────────────┘         └─────────────────┘

┌─────────────────────────────┐
│ menu                        │
├─────────────────────────────┤
│ menu_id PK                  │
│ titre                       │
│ description                 │
│ nombre_personne_minimum     │
│ prix_par_personne           │
│ conditions_menu             │
│ quantite_restante           │
│ theme_id FK                 │────────▶ theme (theme_id, libelle)
│ regime_id FK                │────────▶ regime (regime_id, libelle)
│ actif (BOOL)                │
│ created_at                  │
└──┬──────────────────────────┘
   │ 1,N
   ▼
┌─────────────────────────────┐
│ menu_image                  │
├─────────────────────────────┤
│ image_id PK                 │
│ menu_id FK                  │
│ chemin                      │
│ alt_texte                   │
│ ordre                       │
└─────────────────────────────┘

┌─────────────────────────────┐
│ menu_plat (assoc. N,N)      │
├─────────────────────────────┤
│ menu_id FK                  │────▶ menu
│ plat_id FK                  │────▶ plat
└─────────────────────────────┘

┌─────────────────────────────┐
│ plat                        │
├─────────────────────────────┤
│ plat_id PK                  │
│ titre                       │
│ photo                       │
│ type ENUM(entree|plat|dessert)│
└──┬──────────────────────────┘
   │ N,N
   ▼
┌─────────────────────────────┐
│ plat_allergene              │
├─────────────────────────────┤
│ plat_id FK                  │
│ allergene_id FK             │────▶ allergene (allergene_id, libelle)
└─────────────────────────────┘

┌─────────────────────────────┐
│ commande                    │
├─────────────────────────────┤
│ numero_commande PK (varchar)│
│ utilisateur_id FK           │────▶ utilisateur
│ menu_id FK                  │────▶ menu
│ date_commande               │
│ date_prestation             │
│ heure_livraison             │
│ adresse_livraison           │
│ ville_livraison             │
│ distance_km                 │
│ nombre_personne             │
│ prix_menu                   │
│ prix_livraison              │
│ reduction                   │
│ prix_total                  │
│ statut                      │
│ pret_materiel (BOOL)        │
│ restitution_materiel (BOOL) │
│ motif_annulation            │
│ mode_contact                │
└──┬──────────────────────────┘
   │ 1,N
   ▼
┌─────────────────────────────┐
│ commande_statut_historique  │
├─────────────────────────────┤
│ id PK                       │
│ numero_commande FK          │
│ statut                      │
│ commentaire                 │
│ date_changement             │
└─────────────────────────────┘

┌─────────────────────────────┐
│ avis                        │
├─────────────────────────────┤
│ avis_id PK                  │
│ utilisateur_id FK           │────▶ utilisateur
│ numero_commande FK UQ       │────▶ commande (1 avis par commande max)
│ note (1-5)                  │
│ description                 │
│ statut ENUM(en_attente|valide|refuse)│
│ created_at                  │
└─────────────────────────────┘

┌─────────────────────────────┐
│ horaire                     │
├─────────────────────────────┤
│ horaire_id PK               │
│ jour UQ                     │
│ heure_ouverture             │
│ heure_fermeture             │
│ ordre                       │
└─────────────────────────────┘

┌─────────────────────────────┐
│ contact_message             │
├─────────────────────────────┤
│ id PK                       │
│ titre                       │
│ description                 │
│ email                       │
│ created_at                  │
└─────────────────────────────┘
```

## Cardinalités principales

| Relation | Cardinalité |
|----------|-------------|
| utilisateur ↔ role | (0,N) — (1,N) via `possede` |
| menu ↔ theme | (1,1) — (0,N) |
| menu ↔ regime | (1,1) — (0,N) |
| menu ↔ plat | (0,N) — (0,N) via `menu_plat` |
| plat ↔ allergene | (0,N) — (0,N) via `plat_allergene` |
| menu ↔ image | (0,N) — (1,1) |
| commande ↔ utilisateur | (1,1) — (0,N) |
| commande ↔ menu | (1,1) — (0,N) |
| commande ↔ historique | (1,N) — (1,1) |
| commande ↔ avis | (0,1) — (1,1) |

## Décisions de modélisation justifiées

1. **`numero_commande` en `VARCHAR(50)` plutôt qu'INT auto-incrémenté**
   → Format lisible pour le client (`CMD-20260507-A1B2C3`), insensible aux requêtes par énumération.

2. **Table `commande_statut_historique` séparée**
   → Tracer chronologiquement chaque transition (exigence du sujet : « Le suivi de la commande énumère tous les états de sa commande suivi de la date et l'heure de modification »).

3. **`utilisateur.actif` en BOOLEAN**
   → Permet de désactiver un compte employé sans le supprimer (préserve les FKs et l'historique).

4. **Validation note 1-5 côté PHP, pas via CHECK CONSTRAINT**
   → MariaDB 10.4 a montré une instabilité avec les CHECK CONSTRAINT nommées + FK. Validation déplacée dans le contrôleur (sécurité maintenue).

5. **Tables d'association nommées** (`menu_plat`, `plat_allergene`, `possede`)
   → Conforme aux bonnes pratiques 3FN, évite les listes encodées en VARCHAR.

6. **MongoDB pour les statistiques** (collection `commandes_par_menu`)
   → Document avec compteur incrémenté + historique embarqué = pattern idéal en NoSQL pour des analytics. **Exigence explicite du sujet**.
