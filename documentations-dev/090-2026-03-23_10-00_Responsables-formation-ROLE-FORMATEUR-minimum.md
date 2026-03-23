# 090 — Responsables de formation limités à ROLE_FORMATEUR minimum

**Date** : 2026-03-23 10:00
**Branche** : main

## Fichiers modifiés
- `src/Controller/Admin/FormationCrudController.php`

## Changements

### Champ `responsables` dans le formulaire Formation
`AssociationField` enrichi avec `->setQueryBuilder()` pour filtrer les utilisateurs proposés.

**Avant** : tous les utilisateurs de la base apparaissaient dans la liste.

**Après** : seuls les utilisateurs ayant au moins `ROLE_FORMATEUR` sont proposés :
- `entity.roles LIKE '%ROLE_FORMATEUR%'`
- `entity.roles LIKE '%ROLE_ADMIN%'`
- `entity.roles LIKE '%ROLE_SUPER_ADMIN%'`

Import ajouté : `Doctrine\ORM\QueryBuilder`.

## Raison
Un responsable de formation doit être au minimum formateur. Les simples `ROLE_USER` et `ROLE_STAGIAIRE` ne peuvent pas être désignés responsables d'une formation.
