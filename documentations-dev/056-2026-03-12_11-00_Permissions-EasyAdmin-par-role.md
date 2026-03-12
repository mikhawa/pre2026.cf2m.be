# 056 — Permissions EasyAdmin par rôle

**Date** : 2026-03-12 11:00
**Fichiers modifiés** :
- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/UserCrudController.php`
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/InscriptionCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/PartenaireCrudController.php`
- `src/Controller/Admin/RatingCrudController.php`

## Résumé des permissions appliquées

| CRUD              | ROLE_FORMATEUR       | ROLE_ADMIN                  | ROLE_SUPER_ADMIN |
|-------------------|----------------------|-----------------------------|-----------------|
| Formations        | éditer uniquement    | éditer uniquement           | tout            |
| Works             | tout                 | tout                        | tout            |
| Commentaires      | tout                 | tout                        | tout            |
| ContactMessages   | voir                 | voir                        | tout            |
| Users             | ❌                   | voir/éditer (sauf rôles)    | tout            |
| Inscriptions      | ❌                   | tout                        | tout            |
| Pages             | ❌                   | tout                        | tout            |
| Partenaires       | ❌                   | tout                        | tout            |
| Notes (Rating)    | ❌                   | lecture seule               | lecture seule   |

## Sécurités spécifiques
- **ROLE_ADMIN ne peut pas éditer un ROLE_SUPER_ADMIN** : override `edit()` dans UserCrudController, throw 403
- **Champ `roles`** : caché pour ROLE_ADMIN via `->setPermission('ROLE_SUPER_ADMIN')`
- **Menu** : items cachés selon les permissions (EasyAdmin respecte `->setPermission()`)

## Raison
Conformité avec la table des permissions définie dans `docs/architecture/easyadmin.md`.
