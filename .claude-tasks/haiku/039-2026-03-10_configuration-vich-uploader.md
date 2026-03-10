# 039 — Configuration vich/uploader-bundle

**Date** : 2026-03-10
**Modèle** : Haiku (configuration de bundle, tâche standard)

## Fichiers modifiés/créés
- `config/bundles.php` — enregistrement de `VichUploaderBundle`
- `config/packages/vich_uploader.yaml` — configuration du mapping `user_avatar`
- `public/uploads/avatars/` — dossier de destination créé

## Résumé

Le bundle `vich/uploader-bundle` 2.9.1 était installé via Composer et utilisé dans `User.php` (`#[Vich\Uploadable]`, mapping `user_avatar`) mais n'était pas enregistré ni configuré.

### Configuration mise en place
- **Mapping** : `user_avatar`
- **Destination** : `public/uploads/avatars/`
- **URI prefix** : `/uploads/avatars`
- **Namer** : `SmartUniqueNamer` (nom unique basé sur le nom original)

## Résultat
89/89 tests passent. `php bin/console debug:config vich_uploader` valide.
