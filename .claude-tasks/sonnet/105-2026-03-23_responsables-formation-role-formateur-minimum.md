# 105 — Restriction des responsables de formation à ROLE_FORMATEUR minimum

**Modèle** : Sonnet
**Justification** : Modification d'un champ AssociationField EasyAdmin avec QueryBuilder custom.

## Fichiers modifiés
- `src/Controller/Admin/FormationCrudController.php`

## Résumé

Le champ `responsables` de `FormationCrudController` ne propose désormais que les utilisateurs
ayant au moins `ROLE_FORMATEUR` dans leur tableau de rôles (stocké en JSON).

### Implémentation

`setQueryBuilder` sur l'`AssociationField` avec une clause `WHERE` utilisant trois `LIKE` en `OR` :
- `entity.roles LIKE '%ROLE_FORMATEUR%'`
- `entity.roles LIKE '%ROLE_ADMIN%'`
- `entity.roles LIKE '%ROLE_SUPER_ADMIN%'`

Cette approche est compatible avec le stockage JSON natif de Doctrine pour les tableaux de rôles
et respecte la hiérarchie Symfony sans nécessiter de requête SQL native.
