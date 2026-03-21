# Tâche 086 — Double écriture RevisionService → tables d'historique typées (Phase 3)

**Modèle** : Sonnet
**Justification** : Adaptation d'un service métier critique avec logique de double écriture

## Fichier modifié
- `src/Service/RevisionService.php`

## Résumé
Ajout de la double écriture dans `RevisionService` pour synchroniser en temps réel la table `revision` (JSON) et les 3 tables typées (`formation_history`, `page_history`, `works_history`).

## Changements
- **Imports** : ajout `FormationHistory`, `PageHistory`, `WorksHistory` + leurs repositories
- **Constructeur** : injection de `FormationHistoryRepository`, `PageHistoryRepository`, `WorksHistoryRepository`
- **`createRevision()`** : appel à `saveToTypedHistory()` après persist
- **`updatePendingRevision()`** : appel à `updatePendingTypedHistory()` après mise à jour
- **`applyRevision()`** : appel à `approvePendingTypedHistory()` avant flush

## 3 méthodes privées ajoutées
- `saveToTypedHistory(object, User, bool)` — crée l'entrée typée depuis les factories `fromFormation/fromPage/fromWorks`, STATUS_AUTO_APPROVED si autoApprove, STATUS_PENDING sinon
- `updatePendingTypedHistory(object)` — met à jour les champs de l'entrée PENDING dans la table typée
- `approvePendingTypedHistory(string, int, User)` — marque l'entrée PENDING comme STATUS_APPROVED

## Notes
- `ContentRevisionListener` bénéficie automatiquement du double write via `createRevision()`
- `appliquerVersion()` et `applyPreviousData()` (opérations rares) non couverts — prévu Phase 4

## Résultat
✅ Container compilé, autowiring OK, schéma validé
