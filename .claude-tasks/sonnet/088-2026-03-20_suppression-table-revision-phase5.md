# Tâche 088 — Suppression de la table revision (Phase 5)

**Modèle** : Sonnet
**Justification** : Refactorisation majeure multi-fichiers (service, controllers, twig extension, migration)

## Fichiers modifiés
- `src/Entity/Revision.php`
- `src/Service/RevisionService.php`
- `src/Twig/NavigationExtension.php`
- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `migrations/Version20260320195434.php`

## Fichiers supprimés
- `src/Controller/Admin/RevisionCrudController.php`
- `src/Controller/Admin/FormationRevisionCrudController.php`
- `src/Controller/Admin/PageRevisionCrudController.php`
- `src/Controller/Admin/WorksRevisionCrudController.php`
- `src/Repository/RevisionRepository.php`

## Résumé

### RevisionService
- `createRevision()` : suppression de `$this->em->persist($revision)` + ajout de `$revision->setCreatedAt(new \DateTimeImmutable())` pour le DTO transient
- `updatePendingTypedHistory()` : passage de `private` à `public`
- Nouvelle méthode publique `notifyAuthorFromHistory(FormationHistory|PageHistory|WorksHistory, bool): void` — construit un Revision DTO transient depuis l'historique typé pour les emails

### Revision.php → DTO pur
- Suppression de toutes les annotations Doctrine ORM
- Suppression des imports `Doctrine\ORM\Mapping`, `Doctrine\DBAL\Types\Types`, `App\Repository\RevisionRepository`
- Conservation de tous les getters/setters pour compatibilité templates email

### NavigationExtension
- `RevisionRepository` → `FormationHistoryRepository + PageHistoryRepository + WorksHistoryRepository`
- `getPendingRevisionsCount()` = somme des trois `countPending()`

### DashboardController
- `RevisionRepository` → 3 repos typés
- Menu "Révisions" → lien vers `FormationCrudController` avec badge somme des 3 repos

### Controllers Formation/Page/Works
- Suppression de `RevisionRepository` du constructeur
- `updateEntity()` Formation : `formationHistoryRepo->findPendingForFormation()` + `updatePendingTypedHistory()`
- Approve/reject : bridge supprimé, utilisation de `notifyAuthorFromHistory()`

### Migration
- `DROP TABLE revision` avec suppression des foreign keys

## Résultat
✅ 89/89 tests verts | `doctrine:schema:validate` OK | `cache:clear` OK
