# 059 — Révisions : diff visible + sauvegarde avant application

**Date** : 2026-03-12 13:00
**Fichiers modifiés** :
- `src/Service/RevisionService.php`
- `src/Controller/Admin/RevisionCrudController.php`

## Problèmes résolus

### 1. Diff non visible
Ajout d'un tableau HTML de comparaison sur la page détail de chaque révision :
- Colonne "Valeur actuelle" (état live en BDD)
- Colonne "Valeur proposée ✎" (snapshot de la révision)
- Lignes surlignées en jaune pour les champs réellement modifiés

### 2. Sauvegarde avant application
Avant d'approuver ou restaurer une révision, `applyRevision()` sauvegarde l'état actuel de l'entité comme une révision APPROVED intitulée `[avant restauration]`. Cela garantit un rollback possible à tout moment.

## Architecture
- Table unique `revision` avec discriminant `entityType` ('formation'|'page'|'works')
- Pas de collision : `entityType + entityId` forme une clé composite logique

## Raison
L'admin ne pouvait pas voir le contenu des modifications avant de les valider.
