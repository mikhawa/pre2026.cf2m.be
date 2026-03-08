# 026 - Alias shell dans l'image PHP Docker

**Modèle** : Sonnet
**Justification** : Modification Dockerfile + configuration ENV
**Date** : 2026-03-08

## Fichiers modifiés
- `docker/php/Dockerfile`

## Résumé
- Ajout de `/root/.shrc` avec `alias pbc='php bin/console'` et `alias pbcc='php bin/console cache:clear'`
- `ENV=/root/.shrc` dans le Dockerfile pour que `sh` charge les alias (shell interactif non-login)
- Source de `.shrc` dans `.bashrc` pour compatibilité `bash`

## Résultat
Les alias `pbc` et `pbcc` fonctionnent dans `docker compose exec php sh` et `docker compose exec php bash`.
