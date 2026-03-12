---
modèle: sonnet
justification: Amélioration du système de révisions — diff visible + sauvegarde avant apply
fichiers modifiés:
  - src/Service/RevisionService.php
  - src/Controller/Admin/RevisionCrudController.php
---

## Résumé

### RevisionService — 3 nouvelles méthodes
- `getCurrentSnapshot(Revision): array` — charge l'entité live et retourne son snapshot actuel
- `buildDiffHtml(Revision): string` — tableau HTML comparatif (valeur actuelle vs proposée), champs modifiés surlignés en jaune
- `saveCurrentAsBackup(Revision, User)` (privée) — sauvegarde l'état live comme révision APPROVED avant d'appliquer
- `applyRevision()` modifiée : signature `(Revision, User $reviewer)`, crée un backup avant d'appliquer

### RevisionCrudController
- Ajout d'un champ `TextareaField` (hideOnIndex, hideOnForm) sur la page détail
- Utilise `buildDiffHtml()` via `->formatValue()` pour afficher la comparaison
- Les deux appels à `applyRevision()` passent désormais `$this->getUser()`

## Résultat
- L'admin voit le diff complet (champs modifiés en jaune) sur la page détail d'une révision
- Avant toute approbation/restauration, l'état actuel est sauvegardé en BDD comme révision `[avant restauration]`
