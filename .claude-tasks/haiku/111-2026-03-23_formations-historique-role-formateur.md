# 111 — Formations : historique et révisions auto-approuvées pour ROLE_FORMATEUR

**Modèle** : Haiku
**Justification** : Changements de permission simples sur plusieurs méthodes.

## Fichiers modifiés
- `src/Controller/Admin/FormationCrudController.php`

## Résumé

Les formateurs passent du workflow PENDING au workflow AUTO_APPROVED pour les formations,
et obtiennent l'accès complet à l'historique.

### Permissions historique (`ROLE_ADMIN` → `ROLE_FORMATEUR`)
- `setPermission('historiqueFormation', ...)`
- `historiqueFormation()` : `denyAccessUnlessGranted`
- `approuverHistoriqueFormation()` : `denyAccessUnlessGranted`
- `rejeterHistoriqueFormation()` : `denyAccessUnlessGranted`
- `restaurerHistoriqueFormation()` : `denyAccessUnlessGranted`

### Workflow révisions
- `updateEntity()` : le seuil PENDING passe de `!ROLE_ADMIN` à `!ROLE_FORMATEUR`
- `edit()` : le pré-remplissage avec révision PENDING ne s'applique plus aux formateurs

## Résultat
Les formateurs modifient les formations en auto-approuvé (contenu live mis à jour immédiatement).
Seuls les rôles inférieurs à ROLE_FORMATEUR créent des révisions PENDING.
