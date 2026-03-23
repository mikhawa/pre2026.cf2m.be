# 104 — Attribution de rôles restreinte pour ROLE_ADMIN

**Modèle** : Sonnet
**Justification** : Modification de logique de sécurité dans un controller EasyAdmin existant.

## Fichiers modifiés
- `src/Controller/Admin/UserCrudController.php`

## Résumé

Un `ROLE_ADMIN` peut désormais modifier les rôles des utilisateurs, mais uniquement pour attribuer
`ROLE_FORMATEUR` ou `ROLE_ADMIN`. Le `ROLE_SUPER_ADMIN` reste exclusivement gérable par un
`ROLE_SUPER_ADMIN`.

### Changements effectués

1. **`configureFields()`** : le champ `roles` affiche des choix différents selon le rôle de l'utilisateur
   connecté :
   - `ROLE_SUPER_ADMIN` → tous les rôles (`ROLE_USER`, `ROLE_ADMIN`, `ROLE_SUPER_ADMIN`, `ROLE_FORMATEUR`)
   - `ROLE_ADMIN` → seulement `ROLE_ADMIN` et `ROLE_FORMATEUR`
   - La permission `->setPermission('ROLE_SUPER_ADMIN')` a été supprimée (champ maintenant visible par ROLE_ADMIN)

2. **`updateEntity()`** (nouveau) : garde-fou côté serveur — si l'utilisateur connecté n'est pas
   `ROLE_SUPER_ADMIN`, retire silencieusement `ROLE_SUPER_ADMIN` des rôles avant persistance,
   empêchant toute escalade de privilèges même par manipulation HTTP directe.

### Sécurité
- Protection double : UI restrictive + validation serveur
- Un `ROLE_ADMIN` ne peut pas modifier un `ROLE_SUPER_ADMIN` (contrôle déjà présent dans `edit()`)
