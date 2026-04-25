---
modèle: haiku
justification: Modification CSS uniquement, pas de logique métier
---

## Tâche 163 — Suppression des images de fond, glassmorphisme CSS pur

**Date** : 2026-04-25

### Fichiers modifiés
- `assets/styles/app.css`
- `templates/home/index.html.twig`

### Résumé
Remplacement des `url('/images/hero-bg*.webp')` par des `radial-gradient` multicouches empilés.

#### Dark mode (`body`)
- Avant : photo avec overlay `rgba(5,17,31,0.82)` + `background-attachment: fixed`
- Après : 4 blobs CSS (cyan top-right, navy left, blue bottom-right, vignette bas) + `fixed` desktop uniquement

#### Dark mode (`.cf2m-hero::before` / `::after`)
- `::before` : vignette gauche légèrement renforcée (0.48 → 0)
- `::after` : lueur cyan agrandie (480px → 620px, opacité 0.14 → 0.20)

#### Light mode (`[data-theme="light"] .cf2m-hero`)
- Avant : même photo, overlay transparent bleuté + `fixed`
- Après : `background-color: #c4dff0` + 3 blobs bleutés CSS, `fixed` desktop uniquement

#### Section formations (`#formations`, dark mode)
- Avant : `style` inline avec `formations-bg.webp` + 2 overlays → photo retirée du Twig
- Après : règle CSS `.page-home #formations` avec 4 blobs (vignette top, cyan left, navy right, vignette bottom) + `border-top: 5px solid #38476b`
- Light mode : `!important` retirés (plus nécessaires sans style inline)

### Résultat attendu
- Zéro requête HTTP pour les images de fond
- `backdrop-filter: blur()` toujours actif sur les dégradés CSS → effet glassmorphisme préservé
- Rendu plus "design tech/abstrait" vs texture photographique
