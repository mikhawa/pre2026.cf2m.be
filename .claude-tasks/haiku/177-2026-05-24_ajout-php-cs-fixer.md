# 177 — Ajout de php-cs-fixer

**Date** : 2026-05-24  
**Modèle** : Haiku  
**Justification** : Documentation pure, aucune modification de code

## Fichiers modifiés / créés
- `composer.json` — ajout de `friendsofphp/php-cs-fixer ^3.95` en dépendance dev
- `composer.lock` — mis à jour automatiquement
- `.php-cs-fixer.dist.php` — fichier de configuration créé à la racine du projet

## Résumé
Installation de [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer) v3.95.2 en dépendance de développement.

### Commande exécutée
```bash
composer require --dev friendsofphp/php-cs-fixer
```

### Configuration `.php-cs-fixer.dist.php`
```php
$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder);
```

- **Règle** : `@Symfony` — applique l'ensemble des règles de style Symfony
- **Exclusions** : dossier `var/`, `config/bundles.php`, `config/reference.php` (fichiers auto-générés)

### Utilisation
```bash
# Vérifier sans corriger (dry-run)
vendor/bin/php-cs-fixer fix --dry-run --diff

# Corriger automatiquement
vendor/bin/php-cs-fixer fix
```

## Résultat
✅ Outil installé, configuration de base opérationnelle avec le standard Symfony