---
modèle: haiku
justification: Ajout d'un QueryBuilder sur un AssociationField EasyAdmin — modification mineure
fichiers modifiés:
  - src/Controller/Admin/WorksCrudController.php
---

## Résumé

Le champ « Étudiants » dans le formulaire Works (création/édition) n'affiche plus que les utilisateurs ayant `ROLE_STAGIAIRE`.

## Changements

- `configureFields()` : `AssociationField` `users` — ajout de `setQueryBuilder()` avec filtre `roles LIKE '%ROLE_STAGIAIRE%'`
