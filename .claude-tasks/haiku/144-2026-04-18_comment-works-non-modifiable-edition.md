---
modèle: haiku
justification: Ajout d'une option de formulaire sur un champ existant
fichiers modifiés:
  - src/Controller/Admin/CommentCrudController.php
---

## Résumé

En édition de commentaire, le champ « Work » est maintenant désactivé (`disabled`) — le work lié à un commentaire existant ne peut plus être modifié.

## Changements

- `configureFields()` : `AssociationField` `works` — ajout de `setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT)`
