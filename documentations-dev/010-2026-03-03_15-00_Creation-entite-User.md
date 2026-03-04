# 010 — Création de l'entité User

**Date** : 2026-03-03
**Fichiers créés / modifiés** :
- `src/Entity/User.php` *(créé)*
- `docs/architecture/database-schema.md` *(mis à jour)*

## Contenu de User.php
Entité de référence définissant les conventions du projet.

### Conventions illustrées
- `declare(strict_types=1)` en tête de chaque fichier
- Attributs PHP 8 pour le mapping Doctrine (`#[ORM\*]`)
- `#[ORM\HasLifecycleCallbacks]` + `#[ORM\PrePersist]` pour `createdAt`
- `#[Vich\Uploadable]` sur la classe + `#[Vich\UploadableField]` sur la propriété fichier
- `updatedAt` mis à jour manuellement dans `setAvatarFile()` (requis par VichUploader)
- `#[Assert\*]` pour la validation (NotBlank, Email, Length, Regex, Url, Image)
- `plainPassword` non mappé, effacé via `eraseCredentials()`
- Setters avec retour `static` (interface fluide)
- `__toString()` retourne `userName`
- `@phpstan-ignore property.unusedType` sur `$id` (assigné par Doctrine)
- `/** @var list<string> */` et `/** @var Collection<int, Entity> */` pour le typage PHPDoc
- `id` en `unsigned`, `status` en `smallint unsigned`
- Relations `orphanRemoval: true` sur les collections

### Champs
email, roles, password, plainPassword (non mappé), userName, activationToken, status, resetPasswordToken, resetPasswordRequestedAt, avatarFile (non mappé / VichUploader), avatarName, biography, externalLink1/2/3, createdAt, updatedAt

### Relations
- `OneToMany` → `Comment` (orphanRemoval)
- `OneToMany` → `Rating` (orphanRemoval)

## Mises à jour de database-schema.md
- Schéma User corrigé et complété (tous les champs réels)
- Ajout des entités `Comment` et `Rating` (à créer — marquées TODO)
- Mise à jour des conventions (unsigned, timestamps, VichUploader)
- Mise à jour des relations résumées
