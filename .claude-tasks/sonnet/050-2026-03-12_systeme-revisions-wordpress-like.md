---
modèle: opus (délégué via Agent)
justification: Architecture complexe — nouvelle entité, service, 3 controllers modifiés, CrudController, email, migration
fichiers créés:
  - src/Entity/Revision.php
  - src/Repository/RevisionRepository.php
  - src/Service/RevisionService.php
  - src/Controller/Admin/RevisionCrudController.php
  - templates/emails/revision_pending.html.twig
  - migrations/Version20260312152822.php (générée + exécutée)
fichiers modifiés:
  - src/Controller/Admin/FormationCrudController.php
  - src/Controller/Admin/PageCrudController.php
  - src/Controller/Admin/WorksCrudController.php
  - src/Controller/Admin/DashboardController.php
  - src/Repository/UserRepository.php
---

## Résumé
Système de révisions (type WordPress) pour Formation, Page et Works.

### Règles métier
- Formation + Page, ROLE_FORMATEUR : révision PENDING, contenu live inchangé, email aux admins
- Works, ROLE_FORMATEUR : révision auto-APPROVED, contenu live mis à jour
- ADMIN/SUPER_ADMIN : révision auto-APPROVED, contenu live mis à jour

### Entité Revision
Champs : entityType, entityId, entityTitle, data (JSON snapshot), status (0/1/2), createdBy, createdAt, reviewedBy, reviewedAt, reviewNote

### Actions EasyAdmin
- Approuver (PENDING only) : applique snapshot + status APPROVED
- Rejeter (PENDING only) : status REJECTED, live inchangé
- Restaurer (APPROVED only) : réapplique snapshot au live

### Migration
Version20260312152822.php — table `revision` créée et migrée avec succès.
