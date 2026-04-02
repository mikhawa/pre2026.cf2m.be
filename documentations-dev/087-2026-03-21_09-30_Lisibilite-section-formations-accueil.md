# 087 — Lisibilité section formations accueil

**Date** : 2026-03-21 09:30
**Branche** : feature/structure-for-database

## Fichiers modifiés
- `templates/home/index.html.twig`
- `assets/styles/app.css`

## Changements

### 1. Suppression de la date de publication
Bloc `{% if formation.publishedAt %}` retiré du template — la date n'a pas d'intérêt pour le visiteur.

### 2. Titre "Nos formations" en blanc
Ajout de règles CSS ciblées `.page-home #formations` pour que le label "Catalogue" et le titre "Nos formations" soient blancs sur le fond sombre de la section.

### 3. Fond opaque sur les cartes
`.page-home #formations .cf2m-card` reçoit `background: rgba(255,255,255,0.92)` pour garantir la lisibilité du texte `#4a6070` par-dessus le fond sombre avec overlay noir.

## Raison
Sur la page d'accueil, la section formations a un fond sombre (overlay noir 60%). Le titre navy `var(--cf2m-navy)` et le fond de carte semi-transparent `rgba(255,255,255,0.50)` rendaient le texte peu lisible.
