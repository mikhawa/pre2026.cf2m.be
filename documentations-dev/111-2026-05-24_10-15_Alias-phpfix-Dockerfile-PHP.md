# 111 — Alias `phpfix` dans l'image Docker PHP

**Date** : 2026-05-24 10:15  
**Auteur** : mikhawa

## Fichier modifié
- `docker/php/Dockerfile`

## Résumé
Ajout de l'alias `phpfix='./vendor/bin/php-cs-fixer fix'` dans le bloc des alias shell du conteneur PHP, aux côtés des alias existants (`pbc`, `pbcc`, `fl`, `asset`).

## Raison
Raccourci pratique pour lancer php-cs-fixer depuis le conteneur sans taper le chemin complet.

## Rebuild requis
```bash
docker compose build php
```

## Utilisation
```bash
phpfix          # correction automatique de tout le projet
```
