# 060 — Révision : suppression de la double entrée via previousData

**Modèle** : Sonnet
**Justification** : Modification du service métier + entité + migration + controller

## Problème
Lors de l'approbation d'une révision, `saveCurrentAsBackup` créait une 2e entrée `Revision`
(titre + "[avant restauration]") pour conserver l'état avant modification. Résultat : chaque
approbation doublait les entrées dans la liste des révisions.

## Solution
Ajout d'un champ `previousData` (JSON nullable) directement sur l'entité `Revision`.
- L'état actuel est stocké dans ce champ au moment de l'approbation → **0 nouvelle entrée**
- "Restaurer" applique `previousData` et permute avec l'état courant (undo/redo possible)
- Le bouton "Restaurer" n'apparaît que si `previousData !== null`

## Fichiers modifiés
- `src/Entity/Revision.php` — champ `previousData` + getter/setter
- `src/Service/RevisionService.php` — `applyRevision` stocke dans previousData, ajout `applyPreviousData`, suppression `saveCurrentAsBackup`
- `src/Controller/Admin/RevisionCrudController.php` — `restaurerRevision` utilise `applyPreviousData`, condition displayIf mise à jour
- `migrations/Version20260317100000.php` — ADD COLUMN `previous_data` JSON DEFAULT NULL

## Comportement final
| Action | Avant | Après |
|--------|-------|-------|
| Approuver | 2 entrées (révision + backup) | 1 entrée (previousData dans la révision) |
| Restaurer | Crée encore une entrée backup | Permute previousData ↔ état live |
| Bouton Restaurer | Toujours visible si APPROVED | Visible seulement si previousData non null |
