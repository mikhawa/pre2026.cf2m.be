# 080 — Inclusion des relations ManyToMany dans les snapshots d'historique

**Date** : 2026-03-20 20:30
**Tâche** : 090

## Fichier modifié
- `src/Service/RevisionService.php`

## Résumé
Les changements de responsables/participants (relations ManyToMany) n'apparaissaient pas dans le diff d'historique car ils n'étaient pas inclus dans les snapshots de comparaison.

### Méthodes mises à jour
- `snapshotFormation()` — ajout de `'responsables'` (noms triés)
- `snapshotPage()` — ajout de `'users'` (noms triés)
- `snapshotWorks()` — ajout de `'users'` (noms triés)
- `snapshotFromFormationHistory()` — ajout de `'responsables'`
- `snapshotFromPageHistory()` — ajout de `'users'`
- `snapshotFromWorksHistory()` — ajout de `'users'`
- `buildTypedHistoryDiffHtml()` — ajout des labels `'responsables'` et `'users'`

### Nouvelle méthode privée
- `usersToSortedString(iterable $users): string` — convertit une collection d'utilisateurs en chaîne triée de noms d'utilisateur (`userName`), servant de clé de comparaison stable.

## Raison
Demande utilisateur : "Les changements venant de tables externes, comme les Responsables, ne sont pas visibles". Les relations ManyToMany sont désormais incluses dans tous les snapshots de comparaison.
