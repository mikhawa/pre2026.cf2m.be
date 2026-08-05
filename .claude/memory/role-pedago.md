---
name: role-pedago
description: Hiérarchie et permissions du rôle ROLE_PEDAGO (parallèle à ROLE_ADMIN) — terminé 2026-04-04
metadata:
  type: project
---

## ROLE_PEDAGO — TERMINÉ ✅ (2026-04-04, branche fix/02)

### Hiérarchie
`ROLE_PEDAGO: [ROLE_FORMATEUR, ROLE_STAGIAIRE, ROLE_USER]` — parallèle à ROLE_ADMIN, sans lien.

### Permissions
- Formations : créer + modifier les siennes (auto-approve) — voter `FORMATION_CREATE`
- Works : lecture seule (NEW/EDIT désactivés dans WorksCrudController)
- Pages, Inscriptions, Messages contact, Utilisateurs : accès complet via voter `CONTENT_MANAGER`
- Mails : reçoit préinscriptions + contact, **pas** les mails de révision

### Restriction création utilisateurs
ROLE_PEDAGO (sans ROLE_ADMIN) ne peut attribuer que ROLE_STAGIAIRE / ROLE_FORMATEUR / ROLE_PEDAGO.
Protection à 3 niveaux : `configureFields()` (UI) + `persistEntity()` + `updateEntity()` (serveur).
Pattern utilisé : `isGranted('ROLE_PEDAGO') && !isGranted('ROLE_ADMIN')` → cumul ADMIN+PEDAGO = droits ADMIN.

### Fichiers clés
- `src/Security/Voter/ContentManagerVoter.php` (créé)
- `src/Controller/Admin/UserCrudController.php` (restrictions rôles)
- `src/Repository/UserRepository.php` (`findInscriptionRecipients`, `findContactRecipients`)
