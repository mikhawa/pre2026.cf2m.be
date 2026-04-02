# Tâche 087 — Bascule controllers EasyAdmin + templates vers tables typées (Phase 4)

**Modèle** : Sonnet
**Justification** : Refactorisation majeure de controllers métier + service + templates

## Fichiers modifiés
- `src/Service/RevisionService.php`
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `templates/admin/formation/historique.html.twig`
- `templates/admin/page/historique.html.twig`
- `templates/admin/works/historique.html.twig`

## Résumé

### RevisionService — nouvelles méthodes publiques (section Phase 4)
- `snapshotFromFormationHistory(FormationHistory): array`
- `snapshotFromPageHistory(PageHistory): array`
- `snapshotFromWorksHistory(WorksHistory): array`
- `buildTypedHistoryDiffHtml(array $after, ?array $before): string` — diff git-like entre deux snapshots
- `approuverFormationHistory(FormationHistory, User): void` — applique + marque APPROVED + flush
- `rejeterFormationHistory(FormationHistory, User): void` — marque REJECTED + flush
- `approuverPageHistory`, `rejeterPageHistory`, `approuverWorksHistory`, `rejeterWorksHistory` — idem

### Controllers (Formation, Page, Works)
- Injection de `FormationHistoryRepository` / `PageHistoryRepository` / `WorksHistoryRepository`
- Badge count Historique : depuis typed repos
- Badge "En attente" : depuis typed repos
- `edit()` FormationCrudController : pré-remplissage depuis FormationHistory
- `historiqueFormation/Page/Works()` : chargement depuis typed tables, diffs entre versions consécutives
- Nouvelles routes `approuverHistorique*` et `rejeterHistorique*` avec bridge notification vers ancienne table Revision

### Templates
- `rev.status` → `rev.revisionStatus` (isApproved inclut 1 et 3)
- `#{{ rev.id }}` → `v{{ rev.version }}`
- `rev.statusLabel` → `rev.revisionStatusLabel`
- Bouton "Restaurer" supprimé (feature reportée)

## Résultat
✅ cache:clear OK, schema:validate OK, autowiring OK (3 repositories bien injectés)
