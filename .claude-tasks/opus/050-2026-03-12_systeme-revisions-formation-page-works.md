# 050 — Système de révisions pour Formation, Page et Works

- **Date** : 2026-03-12
- **Modèle** : Opus
- **Justification** : Architecture complexe (nouveau système transversal, service métier, notifications email, CrudController custom avec actions)

## Fichiers créés
- `src/Entity/Revision.php` — Entité Revision avec constantes STATUS_PENDING/APPROVED/REJECTED
- `src/Repository/RevisionRepository.php` — findPendingCount() et findByEntityType()
- `src/Service/RevisionService.php` — createRevision(), applyRevision(), notifyAdmins()
- `src/Controller/Admin/RevisionCrudController.php` — CRUD lecture seule + actions Approuver/Rejeter/Restaurer
- `templates/emails/revision_pending.html.twig` — Email de notification aux admins

## Fichiers modifiés
- `src/Repository/UserRepository.php` — ajout findAdmins()
- `src/Controller/Admin/FormationCrudController.php` — ajout updateEntity() avec gestion révisions
- `src/Controller/Admin/PageCrudController.php` — ajout updateEntity() avec gestion révisions
- `src/Controller/Admin/WorksCrudController.php` — ajout updateEntity() avec révision auto-approuvée
- `src/Controller/Admin/DashboardController.php` — ajout menu Révisions

## Résumé
Système de révisions type WordPress :
- Formation/Page : ROLE_FORMATEUR crée une révision PENDING (contenu live inchangé, email aux admins)
- Works : révision toujours auto-approuvée
- ROLE_ADMIN/SUPER_ADMIN : révision auto-approuvée, contenu live mis à jour
- Actions admin : Approuver, Rejeter, Restaurer depuis EasyAdmin

## Résultat
Code créé, migration à générer via `php bin/console make:migration`
