# 074 — Règles de suppression EasyAdmin (inscriptions, commentaires, contact, notes)

**Date** : 2026-03-18 16:30
**Branche** : `feature/add-icones-into-admin`

## Règles appliquées

| Entité | Avant | Après |
|---|---|---|
| Inscription | DELETE accessible à ROLE_ADMIN | DELETE désactivé pour tous |
| Commentaire | Pas de configureActions (DELETE actif par défaut) | DELETE désactivé pour tous |
| ContactMessage | DELETE non protégé (actif par défaut) | DELETE désactivé pour tous |
| Note (Rating) | DELETE désactivé pour tous | DELETE réservé à ROLE_SUPER_ADMIN |

## Fichiers modifiés

| Fichier | Changement |
|---|---|
| `src/Controller/Admin/InscriptionCrudController.php` | `disable(NEW, DELETE)` — DELETE retiré de setPermission |
| `src/Controller/Admin/CommentCrudController.php` | `configureActions` ajouté avec `disable(NEW, DELETE)` |
| `src/Controller/Admin/ContactMessageCrudController.php` | `disable(NEW, DELETE)` |
| `src/Controller/Admin/RatingCrudController.php` | DELETE retiré de `disable()`, `setPermission(DELETE, 'ROLE_SUPER_ADMIN')` |
