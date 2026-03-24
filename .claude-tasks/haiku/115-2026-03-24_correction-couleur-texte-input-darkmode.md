# 115 — Correction couleur texte input dark mode

**Modèle** : Haiku
**Justification** : Fix CSS mineur, 1 fichier, 2 lignes

## Problème
En dark mode, le texte saisi dans le formulaire de contact (et tout `cf2m-input`) devenait blanc, invisible sur le fond clair `#f8fafc`.

## Cause
Bootstrap 5.3 définit `.form-control { color: var(--bs-body-color) }` avec `var(--bs-body-color)` qui vaut blanc en dark mode. Cette règle surchargeait le `color: #1a2b3c` de `.cf2m-input` car Bootstrap est chargé après `app.css`.

## Fichier modifié
- `assets/styles/app.css` — ajout de `!important` sur `color: #1a2b3c` dans `.cf2m-input` et `.cf2m-input:focus`

## Résultat
Texte toujours sombre (`#1a2b3c`) dans les champs du formulaire, quelle que soit le thème actif.
