# Tâche 090 — Inclusion des relations ManyToMany dans les snapshots d'historique

**Modèle** : Sonnet
**Justification** : Modification de méthodes de service métier

## Fichier modifié
- `src/Service/RevisionService.php`

## Résumé
Ajout d'un helper `usersToSortedString(iterable $users): string` et mise à jour des 6 méthodes snapshot pour inclure :
- `'responsables'` (Formation + FormationHistory) — `usersToSortedString($entity->getResponsables())`
- `'users'` (Page + PageHistory, Works + WorksHistory) — `usersToSortedString($entity->getUsers())`

Labels ajoutés dans `buildTypedHistoryDiffHtml()` :
- `'responsables' => 'Responsables'`
- `'users' => 'Participants'`

## Résultat
✅ 89/89 tests | `cache:clear` OK
Les changements de responsables et participants apparaissent maintenant dans le diff d'historique EasyAdmin.
