# 035 — Alias shell dans l'image PHP Docker

**Date** : 2026-03-08 09:30
**Branche** : Navigation

## Fichiers modifiés
- `docker/php/Dockerfile`

## Résumé des changements

Ajout de deux alias shell disponibles dans `sh` et `bash` :

| Alias | Commande complète |
|-------|-------------------|
| `pbc` | `php bin/console` |
| `pbcc` | `php bin/console cache:clear` |

### Technique
- Les alias sont écrits dans `/root/.shrc`
- La variable d'environnement `ENV=/root/.shrc` est définie → `sh` charge ce fichier au démarrage d'un shell interactif (`docker compose exec php sh`)
- `/root/.bashrc` source `/root/.shrc` → les alias fonctionnent aussi sous `bash`

## Raison
Raccourcis ergonomiques pour les commandes Symfony les plus fréquentes lors du développement dans le conteneur PHP.

## Rebuild requis
```bash
docker compose build php
docker compose up -d
```
