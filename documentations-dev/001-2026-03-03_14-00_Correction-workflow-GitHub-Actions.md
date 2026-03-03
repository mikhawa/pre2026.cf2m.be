# 001 — Correction workflow GitHub Actions

**Date** : 2026-03-03
**Fichier modifié** : `.github/workflows/symfony.yml`

## Changements
- Remplacement de l'action épinglée `shivammathur/setup-php@2cb9b829437ee246e9b3cac53555a39208ca6d28` par `shivammathur/setup-php@v2`
- Suppression des commentaires obsolètes autour de l'action
- Version PHP conservée : `8.5`

## Raison
L'ancien commit épinglé utilisait la commande dépréciée `set-output` et contenait un bug de guillemets dans les chemins (`'"/etc/php/8.5/cli"/..`), provoquant des avertissements lors du passage du workflow sur GitHub.
