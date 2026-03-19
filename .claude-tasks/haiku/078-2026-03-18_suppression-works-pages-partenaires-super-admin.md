---
modèle: haiku
justification: Restriction de permission simple, modification de constantes EasyAdmin
date: 2026-03-18
---

# 078 — Suppression Works, Pages, Partenaires réservée au SUPER_ADMIN

## Fichiers modifiés
- `src/Controller/Admin/WorksCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/PartenaireCrudController.php`

## Résumé
Un administrateur simple (`ROLE_ADMIN`) ne peut plus supprimer les Works, les Pages ni les Partenaires.
La suppression est désormais réservée à `ROLE_SUPER_ADMIN` pour ces trois entités.

## Changements
- `WorksCrudController` : ajout de `->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')`
- `PageCrudController` : `ROLE_ADMIN` → `ROLE_SUPER_ADMIN` sur `Action::DELETE`
- `PartenaireCrudController` : `ROLE_ADMIN` → `ROLE_SUPER_ADMIN` sur `Action::DELETE`

## Résultat
Tableau des suppressions EasyAdmin mis à jour :
| Entité      | Suppression        |
|-------------|--------------------|
| Works       | ROLE_SUPER_ADMIN   |
| Pages       | ROLE_SUPER_ADMIN   |
| Partenaires | ROLE_SUPER_ADMIN   |
