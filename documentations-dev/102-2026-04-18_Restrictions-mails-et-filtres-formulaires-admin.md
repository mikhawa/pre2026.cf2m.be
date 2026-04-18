# 102 — Restrictions mails et filtres formulaires admin

**Date** : 2026-04-18  
**Branche** : main

## Fichiers modifiés

- `src/Service/RevisionService.php`
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `src/Repository/UserRepository.php`
- `docs/architecture/permissions-et-mails.md`

## Résumé des changements

### 1. Formation — `updated_by` mis à jour à la validation

Lors de l'approbation d'une révision de formation (chemin PENDING → APPROVED via `approuverFormationHistory()`, `applyRevision()`, `appliquerVersion()`, `restaurerFormationHistory()`), `formation.updated_by_id` est maintenant renseigné avec l'utilisateur validant.

Chemin auto-approve (admin/super-admin modifie directement) : `FormationCrudController::updateEntity()` appelle désormais `setUpdatedBy($user)` et `setUpdatedAt()` avant `parent::updateEntity()`.

La méthode privée `applyFormation()` accepte un `?User $reviewer = null` — le cas undo/redo (`applyPreviousData`) n'a pas de validateur et laisse `updatedBy` inchangé.

### 2. Restriction des emails de préinscription

`UserRepository::findInscriptionRecipients()` ne retourne plus les `ROLE_ADMIN`. Seuls `ROLE_SUPER_ADMIN` et `ROLE_PEDAGO` reçoivent les emails de nouvelle préinscription, pour éviter les doublons.

### 3. Restriction des emails de formulaire de contact

`UserRepository::findContactRecipients()` ne retourne plus les `ROLE_ADMIN`. Seuls `ROLE_SUPER_ADMIN` et `ROLE_PEDAGO` reçoivent les messages de contact.

### 4. Filtre étudiants dans le formulaire Works

Le champ « Étudiants » (`AssociationField` `users`) dans le formulaire Works (création/édition) n'affiche que les utilisateurs ayant `ROLE_STAGIAIRE`, via `setQueryBuilder()`.

### 5. Filtre responsables dans le formulaire Formation

Le champ « Responsables » (`AssociationField` `responsables`) dans le formulaire Formation était déjà filtré mais incluait `ROLE_ADMIN` et `ROLE_SUPER_ADMIN`. Il est désormais restreint aux `ROLE_FORMATEUR` uniquement.

## Raison

Réduction du bruit de notifications côté email (les admins ne reçoivent plus les mails d'inscription et de contact, qui relèvent de la pédagogie) et cohérence métier dans les formulaires (un responsable de formation est un formateur, un étudiant d'un works est un stagiaire).
