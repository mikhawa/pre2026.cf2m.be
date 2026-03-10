# 045 — Champs couleur sur Formation + widget ColorField EasyAdmin

**Date** : 2026-03-10 13:00
**Fichiers modifiés** :
- `src/Entity/Formation.php`
- `migrations/Version20260310120000.php` (nouveau)
- `src/Controller/Admin/FormationCrudController.php`
- `tests/Entity/FormationTest.php`

## Résumé

Ajout de deux champs de couleur hexadécimale à l'entité `Formation`, avec widget `ColorField` dans l'interface EasyAdmin.

## Entité

Deux nouveaux champs ajoutés dans `Formation` :

```php
#[ORM\Column(length: 7, nullable: true)]
#[Assert\Regex(pattern: '/^#[0-9a-fA-F]{6}$/', message: '...')]
private ?string $colorPrimary = null;

#[ORM\Column(length: 7, nullable: true)]
#[Assert\Regex(pattern: '/^#[0-9a-fA-F]{6}$/', message: '...')]
private ?string $colorSecondary = null;
```

## Migration

```sql
ALTER TABLE formation
    ADD color_primary VARCHAR(7) DEFAULT NULL,
    ADD color_secondary VARCHAR(7) DEFAULT NULL;
```

Exécuter en production : `php bin/console doctrine:migrations:migrate`

## EasyAdmin

```php
yield ColorField::new('colorPrimary', 'Couleur primaire')
    ->hideOnIndex()->setRequired(false)->showValue();

yield ColorField::new('colorSecondary', 'Couleur secondaire')
    ->hideOnIndex()->setRequired(false)->showValue();
```

Le widget `showValue()` affiche la valeur hexadécimale à côté de l'aperçu de couleur.

## Tests
89/89 passent. Nouveau test `testColorFields()` ajouté dans `FormationTest`.
