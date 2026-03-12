---
modèle: sonnet
justification: Sécurisation multi-fichiers de controllers EasyAdmin avec logique de permissions par rôle
fichiers modifiés:
  - src/Controller/Admin/DashboardController.php
  - src/Controller/Admin/UserCrudController.php
  - src/Controller/Admin/FormationCrudController.php
  - src/Controller/Admin/InscriptionCrudController.php
  - src/Controller/Admin/PageCrudController.php
  - src/Controller/Admin/PartenaireCrudController.php
  - src/Controller/Admin/RatingCrudController.php
---

## Résumé
Implémentation des permissions par rôle selon docs/architecture/easyadmin.md.

### DashboardController
- `#[IsGranted]` : ROLE_SUPER_ADMIN → ROLE_FORMATEUR
- Menu : `->setPermission('ROLE_ADMIN')` sur Pages, section Utilisateurs, Utilisateurs, Inscriptions, Notes, Partenaires

### UserCrudController
- `configureActions()` : INDEX/NEW/EDIT/DETAIL → ROLE_ADMIN, DELETE → ROLE_SUPER_ADMIN
- Champ `roles` : `->setPermission('ROLE_SUPER_ADMIN')` (invisible pour ROLE_ADMIN)
- Override `edit()` : 403 si ROLE_ADMIN tente d'éditer un ROLE_SUPER_ADMIN

### FormationCrudController
- NEW + DELETE → ROLE_SUPER_ADMIN uniquement

### InscriptionCrudController
- Toutes les actions → ROLE_ADMIN minimum (NEW déjà désactivé)

### PageCrudController / PartenaireCrudController
- Toutes les actions → ROLE_ADMIN minimum

### RatingCrudController
- NEW/EDIT/DELETE désactivés (lecture seule)
- INDEX/DETAIL → ROLE_ADMIN minimum

## Ce qui n'a PAS changé
- WorksCrudController : FORMATEUR peut créer/modifier
- CommentCrudController : FORMATEUR peut modérer
- ContactMessageCrudController : FORMATEUR peut voir (clarification utilisateur)
