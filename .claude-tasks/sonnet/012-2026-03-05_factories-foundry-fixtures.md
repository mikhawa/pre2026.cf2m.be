# 012 — Factories Foundry et Fixtures

**Modèle** : Sonnet
**Justification** : Configuration de factories avec Faker, AppFixtures — catégorie Sonnet (services/fixtures)
**Date** : 2026-03-05

## Fichiers modifiés

- `config/packages/zenstruck_foundry.yaml` — ajout locale `fr_BE`
- `src/Factory/UserFactory.php` — personnalisé avec states (admin, formateur, banni) + hachage mot de passe
- `src/Factory/FormationFactory.php` — callable defaults, slug dérivé du titre, states (publiee, brouillon)
- `src/Factory/WorksFactory.php` — callable defaults, slug dérivé du titre, state (publie)
- `src/Factory/CommentFactory.php` — state (approuve), clé `approved` (pas `isApproved`)
- `src/Factory/RatingFactory.php` — value 1-5 (pas 1-32767)
- `src/Factory/InscriptionFactory.php` — state (traitee)
- `src/Factory/ContactMessageFactory.php` — state (lu), clé `read` (pas `isRead`)
- `src/Factory/PartenaireFactory.php` — state (inactif), clé `active` (pas `isActive`)
- `src/DataFixtures/AppFixtures.php` — fixtures complètes pour les 9 entités

## Problèmes rencontrés et solutions

### 1. Clés booléennes : PropertyAccessor ne trouve pas le setter
Fondry utilise Symfony PropertyAccessor pour hydrater les entités.
Pour les booleans préfixés `is*`, PropertyAccessor cherche `set{Property}()` avec camelCase complet :
- `isActive` → cherche `setIsActive()` mais l'entité a `setActive()`
- Solution : utiliser les clés sans préfixe `is` → `'active'`, `'approved'`, `'read'`

### 2. `_real()` inutile en Foundry 2.x
En Foundry 2.x, `createMany()` et `createOne()` retournent directement les entités (pas des proxies).
- Suppression de tous les appels `->_real()`

## Résumé des données générées

| Entité | Quantité |
|--------|----------|
| User | 32 (2 admins, 5 formateurs, 25 étudiants) |
| Formation | 10 (8 publiées, 2 brouillons) |
| Works | 32 (2-5 par formation publiée) |
| Comment | 49 (1-4 par travail) |
| Rating | 45 (1-5 par travail, valeur 1-5) |
| Inscription | 27 (traitées + en attente) |
| ContactMessage | 12 (8 non lus, 4 lus) |
| Page | 6 (3 fixes + 3 random) |
| Partenaire | 8 (6 actifs, 2 inactifs) |

## Résultat

`doctrine:fixtures:load` — OK, 256 entrées en base
Tests unitaires entités — OK (88 tests, 279 assertions)
