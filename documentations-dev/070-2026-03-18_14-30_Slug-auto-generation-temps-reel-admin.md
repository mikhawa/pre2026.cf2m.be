# 070 — Génération automatique du slug en temps réel (admin)

**Date** : 2026-03-18 14:30
**Fichiers modifiés** :
- `assets/admin.js`

## Résumé

À la création d'une Formation, Works ou Page dans EasyAdmin, le champ `slug` reste vide jusqu'à soumission. Ajout d'une génération automatique en temps réel lors de la saisie du titre.

## Comportement

- En mode création : le slug se remplit automatiquement au fur et à mesure que le titre est saisi
- Si l'utilisateur modifie manuellement le slug → l'auto-génération s'arrête
- En mode édition : si le slug est déjà rempli au chargement → pas d'écrasement

## Implémentation

Deux fonctions ajoutées dans `assets/admin.js` :

**`slugify(text)`** : convertit un texte en slug URL-friendly
- `normalize('NFD')` pour séparer les caractères accentués des diacritiques
- Supprime les diacritiques (`\u0300-\u036f`)
- Remplace espaces/underscores par des tirets
- Supprime les caractères non alphanumériques

**`initSlugSync()`** : connecte les paires titre/slug par ID EasyAdmin
- IDs : `Formation_title`/`Formation_slug`, `Works_title`/`Works_slug`, `Page_title`/`Page_slug`
- Initialisée sur `DOMContentLoaded`, `turbo:load`, `turbo:frame-load`
