# 016 — Suppression de la dépreciation controller_resolver

**Date** : 2026-03-03 16:35
**Branche** : MarkDownSuite

## Fichier modifié
- `config/packages/doctrine.yaml`

## Résumé des changements
Suppression du bloc :
```yaml
controller_resolver:
    auto_mapping: false
```

## Raison
Option dépréciée depuis DoctrineBundle 3.1 — sera supprimée en 4.0.
N'accepte que `false` depuis 3.0, donc n'a plus aucun effet.
