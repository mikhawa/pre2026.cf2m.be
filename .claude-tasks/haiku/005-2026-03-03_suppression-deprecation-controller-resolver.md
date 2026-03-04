# Tâche 005 — Suppression dépreciation controller_resolver

**Modèle** : Haiku
**Justification** : Correction simple d'une option YAML dépréciée — typo/config basique.

**Date** : 2026-03-03

## Fichiers modifiés
- `config/packages/doctrine.yaml`

## Résumé
Suppression du bloc `controller_resolver.auto_mapping: false` dans `doctrine.yaml`.
Cette option est dépréciée depuis DoctrineBundle 3.1 et sera supprimée en 4.0
(n'accepte que `false` depuis 3.0, donc sans effet utile).

## Résultat
Dépreciation éliminée.
