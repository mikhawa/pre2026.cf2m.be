# 135 — Restriction ROLE_PEDAGO : création d'utilisateur

**Date** : 2026-04-04
**Modèle** : Sonnet
**Justification** : Modification de controller avec logique métier de sécurité

## Fichiers modifiés
- `src/Controller/Admin/UserCrudController.php`

## Résumé
Un `ROLE_PEDAGO` (non-ADMIN) ne peut désormais ni créer ni modifier un utilisateur avec les rôles
`ROLE_ADMIN` ou `ROLE_SUPER_ADMIN`.

### Changements
1. **`configureFields()`** — troisième cas dans la logique `$rolesChoices` :
   - SUPER_ADMIN → tous les rôles
   - PEDAGO (non-ADMIN) → seulement Stagiaire / Formateur / Pédago
   - ADMIN (non-SUPER) → Stagiaire / Formateur / Pédago / Administrateur

2. **`persistEntity()`** — même garde serveur-side que `updateEntity()` :
   - PEDAGO : filtre `ROLE_ADMIN` et `ROLE_SUPER_ADMIN`
   - ADMIN : filtre uniquement `ROLE_SUPER_ADMIN`

## Résultat
165 tests, 384 assertions — OK
