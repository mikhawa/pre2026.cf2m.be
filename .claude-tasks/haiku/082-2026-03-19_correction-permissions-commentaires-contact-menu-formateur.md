# 082 — Correction permissions menu Commentaires et Contact pour ROLE_FORMATEUR

**Modèle** : Haiku
**Justification** : Correction simple de permissions sur des éléments de menu EasyAdmin

## Problème
Un utilisateur `ROLE_FORMATEUR` obtenait l'erreur :
> You don't have enough permissions to run the "index" action on the "App\Controller\Admin\CommentCrudController" or the "index" action has been disabled.

## Cause
Incohérence entre le menu et les contrôleurs :
- `CommentCrudController` restreint toutes les actions à `ROLE_ADMIN`
- `ContactMessageCrudController` n'a pas de restrictions de permissions
- Mais les liens menu dans `DashboardController` n'avaient pas de `setPermission()` pour ces deux entrées

## Fichiers modifiés
- `src/Controller/Admin/DashboardController.php`

## Corrections
- Ajout de `->setPermission('ROLE_ADMIN')` sur le lien "Commentaires"
- Ajout de `->setPermission('ROLE_ADMIN')` sur le lien "Messages de contact"

## Résumé
Les formateurs ne doivent pas avoir accès aux commentaires ni aux messages de contact. Les liens menu sont maintenant cohérents avec les restrictions des contrôleurs.
