# 131 — Remplacement des images hero par celles du thème claude.ai/design

**Date** : 2026-09-14 14:32
**Fichiers modifiés** :
- `public/images/hero-bg.jpg`
- `public/images/hero-portrait.jpg`
- `templates/home/index.html.twig`

## Résumé

Les deux vraies photos utilisées dans le thème claude.ai/design "CF2M Site" ont été
retrouvées (encodées en base64 dans l'export "hors ligne" du canvas, contrairement aux
exports `.dc.html` qui ne référencent qu'un placeholder Unsplash) puis extraites et
utilisées pour remplacer :
- le fond du hero (`hero-bg.jpg`, 172 Ko, 1600×1067 — remplace une photo de 2,7 Mo)
- le portrait circulaire du hero (`hero-portrait.jpg`, 68 Ko, 600×900 — remplace une photo
  de 229 Ko)

Le texte alternatif du portrait (`templates/home/index.html.twig`) a été corrigé de
"Étudiante CF2m" à "Stagiaire CF2m" car la nouvelle photo montre un homme.

## Raison

Suite à la demande explicite de l'utilisateur de faire écraser les images actuelles par
celles du canvas partagé. Point signalé avant modification : les deux photos extraites
sont des photos stock génériques (la photo de fond est le placeholder Unsplash par défaut
de l'outil de design, le portrait un mannequin stock) — l'ancien portrait provenait lui
d'une vraie photo du CF2m. L'utilisateur a confirmé vouloir le remplacement malgré tout.

Les variantes `.webp` (`hero-bg.webp`, `hero-bg-mobile.webp`, `hero-portrait.webp`) et
`hero-groupe.jpg` n'ont pas été touchées : les `.webp` sont des fichiers orphelins non
référencés dans le code, et `hero-groupe.jpg` n'était pas concerné par la demande.
