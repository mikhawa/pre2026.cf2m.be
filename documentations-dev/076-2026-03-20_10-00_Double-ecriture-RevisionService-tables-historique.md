# 076 — Double écriture RevisionService → tables d'historique typées (Phase 3)

**Date** : 2026-03-20 10:00
**Tâche** : 086

## Fichier modifié
- `src/Service/RevisionService.php`

## Résumé
`RevisionService` écrit désormais simultanément dans :
- L'ancienne table `revision` (JSON polymorphique) — inchangée
- Les nouvelles tables typées `formation_history` / `page_history` / `works_history`

Les 3 méthodes impactées : `createRevision()`, `updatePendingRevision()`, `applyRevision()`.
3 méthodes privées ajoutées : `saveToTypedHistory()`, `updatePendingTypedHistory()`, `approvePendingTypedHistory()`.

## Raison
Phase 3 du remplacement de la table `revision` polymorphique. La double écriture garantit que les nouvelles tables sont alimentées en temps réel, sans risque pour le système existant.
