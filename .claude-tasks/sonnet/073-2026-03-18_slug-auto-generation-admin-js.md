# 073 — Génération automatique du slug en temps réel (Formation, Works, Page)

**Modèle** : Sonnet
**Justification** : Ajout JS dans un entrypoint existant, logique simple

## Problème

À la création d'une Formation, Works ou Page dans EasyAdmin, le champ `slug` ne se remplissait pas automatiquement en fonction du titre.

## Solution

Ajout dans `assets/admin.js` :
- Fonction `slugify()` : convertit un texte en slug (gestion des accents français via `normalize('NFD')`)
- Fonction `initSlugSync()` : écoute les `input` sur le champ `title` et met à jour `slug` en temps réel
- Ne remplace pas un slug déjà saisi (détecte si slug est pré-rempli au chargement ou édité manuellement)

## IDs utilisés (convention EasyAdmin 4)

EasyAdmin nomme le form avec `EntityDto::getName()` = `basename(FQCN)`, donc :
- `Formation_title` / `Formation_slug`
- `Works_title` / `Works_slug`
- `Page_title` / `Page_slug`

## Fichiers modifiés

- `assets/admin.js`
