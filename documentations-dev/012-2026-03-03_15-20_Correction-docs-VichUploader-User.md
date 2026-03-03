# 012 — Correction documentation : VichUploader maintenu sur User

**Date** : 2026-03-03
**Fichiers modifiés** :
- `docs/architecture/database-schema.md` *(corrigé)*

## Changements
- Rétablissement de la référence VichUploader sur le champ `avatar_name` de User
- Rétablissement de la note sur `avatarFile` (champ non mappé géré par VichUploader)
- Rétablissement de la convention `updatedAt` mis à jour via `setAvatarFile()` (VichUploader)
- Le mapping `user_avatars` dans `easyadmin.md` était déjà correct, aucune modification nécessaire

## Contexte
User.php utilise bien `#[Vich\Uploadable]` + `#[Vich\UploadableField(mapping: 'user_avatar')]` pour la gestion de l'avatar.
