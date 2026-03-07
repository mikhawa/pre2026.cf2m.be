# 020 — Navbar couleur uniforme sur toutes les pages

**Date** : 2026-03-07
**Modèle** : Sonnet
**Justification** : Correction CSS ciblée, pas de logique métier.

## Problème

Sur la home, la navbar (`rgba(6, 14, 26, 0.45)` + `backdrop-filter: blur`) flotte sur le hero foncé → rendu sombre.
Sur les pages intérieures (fond blanc), le même backdrop filtre le blanc → navbar délavée/claire.

## Fichier modifié

- `assets/styles/app.css` — règle `body:not(.page-home):not(.page-login) .cf2m-navbar` : ajout de `background: var(--cf2m-dark)` + désactivation du `backdrop-filter`.

## Résultat

La navbar a le même rendu sombre (`#08111e`) sur toutes les pages.
