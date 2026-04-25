# 165 — Slideshow CSS dans le cercle hero (portrait → groupe)

**Modèle** : Haiku
**Justification** : Animation CSS pure, aucune logique métier

## Fichiers modifiés
- `public/images/hero-groupe.jpg` — photo de groupe redimensionnée (800×600, 97 Ko) depuis `datas/20250513_151917.jpg`
- `templates/home/index.html.twig` — deux `<img>` avec classes `.cf2m-hero-slide--portrait` et `.cf2m-hero-slide--groupe` en remplacement du `<picture>`
- `assets/styles/app.css` — remplacement des règles `.cf2m-hero-portrait img` par `.cf2m-hero-slide`, `.cf2m-hero-slide--portrait`, `.cf2m-hero-slide--groupe` + `@keyframes cf2m-hero-groupe`

## Résumé
Slideshow CSS pur dans le cercle hero : le portrait reste visible 5 s (z-index 0, toujours affiché), puis la photo de groupe apparaît en fondu (z-index 1, opacity 0→1) pendant 5 s, puis disparaît (1→0). Cycle de 11 s avec 0,5 s de transition de chaque côté. Aucun JS requis.
