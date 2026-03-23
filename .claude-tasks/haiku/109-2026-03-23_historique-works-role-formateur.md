# 109 — Historique Works accessible aux formateurs

**Modèle** : Haiku
**Justification** : Changement de permission simple sur deux lignes.

## Fichiers modifiés
- `src/Controller/Admin/WorksCrudController.php`

## Résumé

L'action "Historique" des Works était restreinte à `ROLE_ADMIN`.
Elle est désormais accessible à `ROLE_FORMATEUR` et supérieurs.

### Modifications
- `configureActions()` : `setPermission('historiqueWorks', 'ROLE_ADMIN')` → `ROLE_FORMATEUR`
- `historiqueWorks()` : `denyAccessUnlessGranted('ROLE_ADMIN')` → `ROLE_FORMATEUR`

Note : les actions approuver/rejeter/restaurer restent à `ROLE_ADMIN`.
