---
name: historique-typees
description: Refactorisation de la table revision polymorphique en 3 tables d'historique typées (formation/page/works) — terminé, 5 phases
metadata:
  type: project
---

## Refactorisation tables d'historique typées — TERMINÉE ✅
**État au 2026-03-20 — 5 phases terminées.**

### Objectif
Remplacer la table `revision` polymorphique (JSON) par 3 tables d'historique typées :
- `formation_history` + `formation_history_responsable`
- `page_history` + `page_history_user`
- `works_history` + `works_history_user`

### Phase 1 ✅ TERMINÉE (tâche 084)
Fichiers créés :
- `src/Entity/Trait/RevisionWorkflowTrait.php` — trait partagé (statuts 0=pending/1=approved/2=rejected/3=auto, champs workflow)
- `src/Entity/FormationHistory.php` — snapshot typé + `fromFormation(Formation, User, int): self`
- `src/Entity/PageHistory.php` — snapshot typé + `fromPage(Page, User, int): self`
- `src/Entity/WorksHistory.php` — snapshot typé + `fromWorks(Works, User, int): self`
- `src/Repository/FormationHistoryRepository.php` — `getNextVersion()`, `findHistoryForFormation()`, `findPendingForFormation()`, `countPending()`
- `src/Repository/PageHistoryRepository.php` — idem pour Page
- `src/Repository/WorksHistoryRepository.php` — idem pour Works
- `migrations/Version20260320113106.php` — 6 tables créées, **migration déjà exécutée**

### Phase 2 ✅ TERMINÉE (tâche 085)
`src/Command/MigrateRevisionsCommand.php` créé. Options `--dry-run` et `--force`. Migration exécutée : 11 formations, 3 pages, 12 works, 0 ignorées.

### Phase 3 ✅ TERMINÉE (tâche 086)
`RevisionService` écrit en double (table `revision` + tables typées). Méthodes impactées : `createRevision()`, `updatePendingRevision()`, `applyRevision()`. `ContentRevisionListener` couvert automatiquement.

### Phase 4 ✅ TERMINÉE (tâche 087)
Controllers Formation/Page/Works basculés vers tables typées. RevisionService +9 méthodes. Templates mis à jour (`rev.revisionStatus`, `v{{ rev.version }}`). Bridge notification garde l'ancienne Revision pour les emails.

### Phase 5 ✅ TERMINÉE (tâche 088)
`DROP TABLE revision` — Migration `Version20260320195434`. `Revision.php` → DTO transient pur. `RevisionRepository`, `RevisionCrudController` et 3 sous-controllers supprimés. `RevisionService.notifyAuthorFromHistory()` gère les emails. 89/89 tests verts.
