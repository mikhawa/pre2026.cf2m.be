# 044 — Fond hero-bg.jpg sur toutes les pages frontend

**Date** : 2026-03-12
**Modèle** : Sonnet
**Justification** : Modification CSS multi-pages, ajustements de lisibilité et cohérence visuelle

## Fichiers modifiés
- `assets/styles/app.css`

## Résumé
Application de `public/images/hero-bg.jpg` comme fond d'écran sur toutes les pages frontend, identiquement à la page d'accueil.

## Changements CSS

### 1. `body` — fond hero-bg.jpg fixe
- Remplacé `background: var(--cf2m-white)` par le fond sombre avec `hero-bg.jpg`
- `background-attachment: fixed` pour un effet de parallaxe cohérent
- Mêmes gradients radiaux que la section `.cf2m-hero`

### 2. Pages intérieures (hors home, hors login)
- `.cf2m-section-title` → couleur blanche (était `var(--cf2m-navy)`)
- `.text-muted` → `var(--cf2m-muted)` (blanc atténué)
- Breadcrumbs Bootstrap → couleurs claires sur fond sombre
- `.cf2m-card` → opacité augmentée à `rgba(255,255,255,0.88)` pour lisibilité du texte sombre à l'intérieur des cartes

### 3. Page works/show
- `.cf2m-work-body` → `background: transparent` (était `var(--cf2m-light)`)
- Le fond hero-bg.jpg est maintenant visible entre les blocs de contenu

## Résultat
Toutes les pages frontend affichent le même fond sombre avec l'image hero-bg.jpg. Les cartes glassmorphisme restent lisibles grâce à leur opacité plus élevée sur les pages intérieures.
