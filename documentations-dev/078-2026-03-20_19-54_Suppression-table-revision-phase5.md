# 078 — Suppression de la table revision (Phase 5)

**Date** : 2026-03-20 19:54
**Tâche** : 088

## Fichiers modifiés
- `src/Entity/Revision.php` — suppression des annotations ORM → DTO transient pur
- `src/Service/RevisionService.php` — suppression de `persist($revision)`, `createRevision()` initialise `createdAt` manuellement, `updatePendingTypedHistory()` rendu public, ajout de `notifyAuthorFromHistory()`
- `src/Twig/NavigationExtension.php` — `RevisionRepository` remplacé par les 3 repos typés
- `src/Controller/Admin/DashboardController.php` — `RevisionRepository` remplacé, menu "Révisions" pointant sur `FormationCrudController`, badge depuis somme des repos typés
- `src/Controller/Admin/FormationCrudController.php` — `RevisionRepository` supprimé du constructeur, `updateEntity()` utilise typed repo + `updatePendingTypedHistory()`, approve/reject utilisent `notifyAuthorFromHistory()`
- `src/Controller/Admin/PageCrudController.php` — idem
- `src/Controller/Admin/WorksCrudController.php` — idem
- `migrations/Version20260320195434.php` — `DROP TABLE revision`

## Fichiers supprimés
- `src/Controller/Admin/RevisionCrudController.php`
- `src/Controller/Admin/FormationRevisionCrudController.php`
- `src/Controller/Admin/PageRevisionCrudController.php`
- `src/Controller/Admin/WorksRevisionCrudController.php`
- `src/Repository/RevisionRepository.php`

## Résumé
La table `revision` (JSON polymorphique) est définitivement supprimée. `Revision.php` devient un DTO transient utilisé uniquement pour les templates d'emails. Les notifications (approve/reject) passent désormais par `notifyAuthorFromHistory()` qui construit le DTO à partir de l'historique typé. Tous les controllers EasyAdmin lisent et écrivent exclusivement dans `formation_history`, `page_history`, `works_history`.

## Raison
Phase 5 (finale) du remplacement de la table `revision` polymorphique. 89/89 tests verts, `doctrine:schema:validate` OK.
