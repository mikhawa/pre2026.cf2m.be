# 037 — Couleurs primaire/secondaire sur Formation + EasyAdmin ColorField

**Date** : 2026-03-10
**Modèle** : Haiku (modification d'entité simple + CRUD EasyAdmin)
**Justification** : Ajout de champs, migration, mise à jour du CRUD — tâche CRUD standard

## Fichiers modifiés
- `src/Entity/Formation.php` — 2 nouveaux champs + getters/setters
- `migrations/Version20260310120000.php` — nouvelle migration (créée manuellement, BDD Docker non disponible)
- `src/Controller/Admin/FormationCrudController.php` — 2 x ColorField
- `tests/Entity/FormationTest.php` — tests colorFields + assertions dans testDefaultValues et testSettersReturnStatic

## Résumé

Ajout de `colorPrimary` et `colorSecondary` à `Formation` :
- Type : `varchar(7)` nullable
- Validation : `Assert\Regex` format `#rrggbb`
- Migration : `Version20260310120000` — `ALTER TABLE formation ADD color_primary VARCHAR(7) DEFAULT NULL, ADD color_secondary VARCHAR(7) DEFAULT NULL`
- EasyAdmin : `ColorField` (`showValue()`, `hideOnIndex()`) pour les 2 champs

## Résultat
89/89 tests passent.
