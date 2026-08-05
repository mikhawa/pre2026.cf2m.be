---
name: conventions-entites
description: Conventions de code pour les entités Doctrine du projet (voir User.php comme référence)
metadata:
  type: project
---

## Conventions entités (voir User.php comme référence)
- `declare(strict_types=1)` obligatoire
- Attributs PHP 8 Doctrine (`#[ORM\*]`)
- `#[ORM\PrePersist]` pour `createdAt`, `updatedAt` mis à jour dans le setter VichUploader
- `#[Vich\Uploadable]` + `#[Vich\UploadableField]` pour les fichiers (y compris User.avatarFile)
- `#[Assert\*]` pour toute validation
- `plainPassword` non mappé, effacé via `eraseCredentials()`
- Setters retournent `static`, `__toString()` retourne le champ principal
- `id` toujours `unsigned`, `status` en `smallint unsigned`
- `orphanRemoval: true` sur les collections OneToMany
- PHPDoc : `/** @var list<string> */` et `/** @var Collection<int, Entity> */`

## Entités à créer (référencées mais manquantes)
- `Comment` (référencée dans User.php)
- `Rating` (référencée dans User.php)
