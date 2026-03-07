# 030 — Navbar couleur uniforme sur toutes les pages

**Date** : 2026-03-07 09h30

## Fichier modifié

- `assets/styles/app.css`

## Résumé

Sur les pages intérieures, la navbar recevait désormais `background: var(--cf2m-dark)` + `backdrop-filter: none`, ce qui reproduit visuellement le rendu de la home (fond sombre `#08111e`).

## Raison

La semi-transparence `rgba(6, 14, 26, 0.45)` combinée au `backdrop-filter` produisait un rendu sombre uniquement quand le fond derrière la navbar était sombre (hero de la home). Sur fond blanc des pages intérieures, le résultat était une navbar délavée/claire.
