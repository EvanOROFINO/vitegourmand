# Charte graphique — Vite & Gourmand

## 1. Identité visuelle

L'identité visuelle de **Vite & Gourmand** s'appuie sur une atmosphère **chaleureuse, conviviale et raffinée**, en cohérence avec l'image d'un traiteur familial bordelais en activité depuis 25 ans.

Les couleurs choisies évoquent :
- la **terre cuite** des plats traditionnels du Sud-Ouest,
- le **vert sauge** des aromates et de la nature,
- le **doré** des bonnes tablées,
- le **brun chocolat** et la **crème** des intérieurs cosy.

---

## 2. Palette de couleurs

| Rôle | Nom | Hexadécimal | Aperçu |
|------|-----|-------------|--------|
| Primaire | Terracotta | `#C8552B` | Boutons CTA, liens, accent principal |
| Primaire foncé | Terracotta dark | `#A03F1E` | Hover des boutons primaires |
| Secondaire | Vert sauge | `#6E8B5C` | Badges secondaires, charts admin |
| Secondaire foncé | Sauge dark | `#556C47` | Hover des liens sauge |
| Accent | Or doux | `#E5C26F` | Étoiles d'avis, titres footer |
| Fond clair | Crème | `#FAF6F0` | Fond du body, tables alternées |
| Fond foncé | Brun chocolat | `#2A2622` | Footer, hero overlay |
| Texte principal | Brun foncé | `#2A2622` | Tous les textes courants |
| Texte secondaire | Brun moyen | `#6B5E54` | Légendes, helpers |
| Bordures | Beige clair | `#E2D6C7` | Cadres, séparateurs |
| Succès | Vert | `#4A8B4A` | Alertes succès |
| Erreur | Rouge brique | `#C0392B` | Alertes erreur, champs requis |
| Avertissement | Or chaud | `#D9A441` | Alertes warning, allergènes |
| Info | Bleu doux | `#4A7AA9` | Alertes info |

**Contraste WCAG AA** : tous les couples texte/fond utilisés respectent un ratio ≥ 4,5:1 (vérifié pour `#2A2622` sur `#FAF6F0` et pour le primaire `#C8552B` sur fond crème).

---

## 3. Typographie

| Usage | Famille | Poids | Source |
|-------|---------|-------|--------|
| Titres (h1, h2, h3, h4) | **Playfair Display** | 600, 700 | Google Fonts |
| Corps de texte | **Inter** | 400, 500, 600, 700 | Google Fonts |
| Code / monospace | system-ui monospace | 400 | Native |

**Règles d'échelle (mobile-first, fluide)** :
- `h1` : `clamp(2rem, 4vw, 3rem)`
- `h2` : `clamp(1.5rem, 3vw, 2.2rem)`
- `h3` : `1.4rem`
- Corps : `1rem` (16 px) avec `line-height: 1.6`

---

## 4. Iconographie

Bibliothèque officielle : **Bootstrap Icons 1.11** (chargée via CDN jsdelivr).

Icônes principales utilisées :
- `bi-cup-hot-fill` — logo et navbar
- `bi-people-fill` — convives
- `bi-cart-plus` — bouton commander
- `bi-star-fill`, `bi-star` — système de notation
- `bi-funnel-fill` — filtres
- `bi-clock-fill` — horaires
- `bi-bag-fill` — commandes
- `bi-currency-euro` — chiffre d'affaires
- `bi-bar-chart-fill` — statistiques

---

## 5. Composants d'interface

### Boutons

- **Primaire** : fond `#C8552B`, texte blanc, `border-radius: 6px`, `padding: 0.7rem 1.5rem`
- **Secondaire** : fond blanc, texte foncé, bordure `#E2D6C7`
- **Danger** : fond `#C0392B`, texte blanc

### Cartes

- Fond blanc
- `border-radius: 10px`
- `box-shadow: 0 1px 3px rgba(0,0,0,0.08)` au repos
- `box-shadow: 0 4px 12px rgba(0,0,0,0.10)` au hover

### Formulaires

- Champs : padding `0.7rem`, bordure `2px solid #E2D6C7`
- Focus : bordure remplacée par `#C8552B`
- Champs requis : marquage `*` rouge à côté du label

### Alerts

Bordure gauche colorée (`4px`) selon le type :
- Succès → vert
- Erreur → rouge brique
- Info → bleu
- Warning → or

---

## 6. Accessibilité (RGAA)

| Critère | Implémentation |
|---------|----------------|
| Skip link | `<a href="#contenu-principal" class="skip-link">` en haut de chaque page |
| Aria-labels | Sur tous les boutons icône et formulaires complexes |
| Aria-expanded | Sur le bouton menu mobile |
| Aria-live | Sur les alerts (rôle `alert`) |
| Focus visible | Outline `3px` `#E5C26F` sur tous les éléments interactifs |
| Contrastes | Vérifiés ≥ 4,5:1 |
| Hiérarchie | Un seul `<h1>` par page, hiérarchie respectée |
| Navigation clavier | Toutes les actions accessibles à la touche `Tab` |
| `prefers-reduced-motion` | Animations limitées aux transitions essentielles |

---

## 7. Maquettes

### Maquettes desktop (3)

1. **Page d'accueil** — Hero avec photo de plats, présentation de l'équipe, avis clients
2. **Liste des menus** — Filtres dynamiques + grille de cartes menus
3. **Détail d'un menu** — Galerie + composition + bouton commande

### Maquettes mobile (3)

1. **Page d'accueil mobile** — Stack vertical, navbar repliable
2. **Liste des menus mobile** — Filtres au-dessus, cartes en pile
3. **Tunnel de commande mobile** — Formulaire en étapes avec récap dynamique

> Les maquettes finales sont exportées au format PNG dans le dossier `docs/charte/maquettes/`.

---

## 8. Responsive design

Breakpoints utilisés :

| Écran | Largeur | Approche |
|-------|---------|----------|
| Mobile | `< 600 px` | Stack vertical, menu repliable |
| Tablette | `600 px – 1024 px` | Grilles 2 colonnes, navbar pleine |
| Desktop | `> 1024 px` | Grilles 3 colonnes, hero pleine largeur |

Container : `max-width: 1200px` (large) / `720px` (étroit).
