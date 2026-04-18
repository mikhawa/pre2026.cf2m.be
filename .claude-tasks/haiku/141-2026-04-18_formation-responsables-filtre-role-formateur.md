---
modèle: haiku
justification: Modification d'un QueryBuilder existant sur un AssociationField EasyAdmin
fichiers modifiés:
  - src/Controller/Admin/FormationCrudController.php
---

## Résumé

Le champ « Responsables » dans le formulaire Formation (création/édition) n'affiche plus que les utilisateurs ayant `ROLE_FORMATEUR`. Les `ROLE_ADMIN` et `ROLE_SUPER_ADMIN` ont été retirés du filtre.

## Changements

- `configureFields()` : `AssociationField` `responsables` — `setQueryBuilder()` simplifié à `roles LIKE '%ROLE_FORMATEUR%'` uniquement
