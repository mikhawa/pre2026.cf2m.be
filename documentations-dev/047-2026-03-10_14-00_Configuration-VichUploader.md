# 047 — Configuration vich/uploader-bundle

**Date** : 2026-03-10 14:00
**Fichiers modifiés** :
- `config/bundles.php`
- `config/packages/vich_uploader.yaml` (nouveau)
- `public/uploads/avatars/` (dossier créé)

## Résumé

Le bundle `vich/uploader-bundle` 2.9.1 était présent dans Composer et annoté dans `User.php` mais n'était ni enregistré ni configuré.

## Corrections

**`config/bundles.php`** — ajout :
```php
Vich\UploaderBundle\VichUploaderBundle::class => ['all' => true],
```

**`config/packages/vich_uploader.yaml`** :
```yaml
vich_uploader:
    db_driver: orm
    mappings:
        user_avatar:
            uri_prefix: /uploads/avatars
            upload_destination: '%kernel.project_dir%/public/uploads/avatars'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```

## Usage dans User.php
```php
#[Vich\Uploadable]
class User {
    #[Vich\UploadableField(mapping: 'user_avatar', fileNameProperty: 'avatarName')]
    private ?File $avatarFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarName = null;
}
```

Les fichiers uploadés sont stockés dans `public/uploads/avatars/` et accessibles via `/uploads/avatars/{filename}`.

Pour afficher un avatar en Twig : `{{ vich_uploader_asset(user, 'avatarFile') }}`
