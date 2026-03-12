# 058 — Système de révisions pour Formation, Page et Works

**Date** : 2026-03-12 12:00

## Fichiers créés
- `src/Entity/Revision.php`
- `src/Repository/RevisionRepository.php`
- `src/Service/RevisionService.php`
- `src/Controller/Admin/RevisionCrudController.php`
- `templates/emails/revision_pending.html.twig`

## Fichiers modifiés
- `src/Repository/UserRepository.php` — méthode `findAdmins()`
- `src/Controller/Admin/FormationCrudController.php` — constructeur + `updateEntity()`
- `src/Controller/Admin/PageCrudController.php` — constructeur + `updateEntity()`
- `src/Controller/Admin/WorksCrudController.php` — constructeur + `updateEntity()`
- `src/Controller/Admin/DashboardController.php` — menu item Révisions

## Résumé
Implémentation d'un système de révisions inspiré de WordPress pour les entités Formation, Page et Works.

### Règles métier
- **Formation + Page** : quand un `ROLE_FORMATEUR` modifie, une révision PENDING est créée. Le contenu live reste inchangé. Un email est envoyé aux administrateurs.
- **Works** : la révision est toujours auto-approuvée (APPROVED), le contenu live est mis à jour normalement.
- **ROLE_ADMIN / ROLE_SUPER_ADMIN** : la révision est auto-approuvée, le contenu live est mis à jour normalement.
- Les administrateurs peuvent Approuver, Rejeter ou Restaurer des révisions depuis EasyAdmin.

### Entité Revision
- Champs : entityType, entityId, entityTitle, data (JSON snapshot), status (PENDING=0, APPROVED=1, REJECTED=2), createdBy, createdAt, reviewedBy, reviewedAt, reviewNote
- Constantes de classe pour les statuts

### RevisionService
- `createRevision()` : crée un snapshot des champs principaux de l'entité
- `applyRevision()` : applique le snapshot JSON à l'entité live
- `notifyAdmins()` : envoie un email à tous les ROLE_ADMIN et ROLE_SUPER_ADMIN

## Raison
Permettre aux formateurs de proposer des modifications qui doivent être validées par un administrateur, tout en gardant un historique des changements.
