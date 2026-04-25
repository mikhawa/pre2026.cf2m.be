# 106 — Remplacement du portrait circulaire par une photo de groupe dans le hero

**Date** : 2026-04-25 18:45
**Branche** : fix/06-divers-bugs

## Fichiers modifiés
- `public/images/hero-portrait.jpg`
- `public/images/hero-portrait.webp`
- `templates/home/index.html.twig`
- `assets/styles/app.css`

## Résumé
La photo de groupe `datas/20250513_151917.jpg` (Samsung Galaxy S25+, 4000×3000) a été redimensionnée à 900×675 (117 Ko) via PHP/GD et placée en remplacement du portrait circulaire dans la colonne droite du hero.

Le cadre circulaire (border-radius: 50%) et l'anneau SVG décoratif ont été remplacés par un cadre rectangulaire arrondi (border-radius: 16px) adapté au format paysage 4:3.

## Raison
Demande client : afficher une photo authentique de stagiaires CF2m plutôt qu'un portrait fictif.
