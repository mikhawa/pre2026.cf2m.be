# 130 — Fond white mode des pages intérieures inspiré du thème claude.ai/design

**Date** : 2026-09-14 14:20
**Fichier modifié** : `assets/styles/app.css`

## Résumé

L'utilisateur a partagé un thème conçu sur claude.ai/design ("CF2M Site") et a demandé
que le fond des pages (hors accueil) en white mode s'en inspire. L'export du design
(`datas/canvas-claude/`) a été analysé : il ne fournit pas de nouvelle image de fond
(le hero réutilise `/images/hero-bg.jpg`, déjà présent dans le projet), mais définit une
palette de fond beaucoup plus claire pour le mode clair : `--cf2m-bg:#f5f7f9` (fond
général) et `--cf2m-panel:#ffffff` (cartes/panneaux).

Le fond `body` en light mode (`[data-theme="light"] body`) est la seule règle qui
s'applique réellement aux « autres pages » (fiche formation, contact, dashboard,
profil, etc.) — l'accueil et la page de connexion ont chacun leur propre fond dédié
(hero photo, gradients). Sa couleur a été changée de `#c4dff0` (bleu ciel saturé) vers
`#f5f7f9` (gris quasi-blanc), reprise telle quelle du thème partagé.

## Raison

Rapprocher visuellement les pages intérieures du site du thème de référence fourni par
l'utilisateur, sans reconstruire de nouvelles maquettes ni importer de modèle de données
fictif (cf. décision similaire prise pour le restyle "raffiné white/dark" du 2026-08-05).
Changement volontairement minimal : une seule valeur de couleur, dans le fichier CSS déjà
responsable du light mode — l'accueil et le login gardent leur traitement existant.
