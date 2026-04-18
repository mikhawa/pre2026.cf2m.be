---
modèle: haiku
justification: Modification simple d'une méthode privée de service — ajout d'un paramètre et appel de setter
fichiers modifiés:
  - src/Service/RevisionService.php
  - src/Controller/Admin/FormationCrudController.php
---

## Résumé

Lors de la validation (approbation) d'une révision de formation, `formation.updated_by_id` est maintenant mis à jour avec l'utilisateur qui a validé.

## Changements

- `applyFormation()` : signature `?User $reviewer = null` + appel `setUpdatedBy($reviewer)` si non null
- `applyRevision()` : passe `$reviewer` à `applyFormation()`
- `appliquerVersion()` : passe `$reviewer` à `applyFormation()`
- `approuverFormationHistory()` : passe `$reviewer` à `applyFormation()`
- `restaurerFormationHistory()` : passe `$reviewer` à `applyFormation()`
- `applyPreviousData()` : aucun User disponible → `updatedBy` inchangé (undo/redo sans validateur)
- `FormationCrudController::updateEntity()` (chemin auto-approve) : `setUpdatedBy($user)` + `setUpdatedAt()` avant `parent::updateEntity()`
