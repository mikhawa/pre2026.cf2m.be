# 011 — Tests unitaires des entités

**Modèle** : Sonnet
**Justification** : Tests PHPUnit (catégorie Sonnet selon models.md)
**Date** : 2026-03-04

## Fichiers créés
- `tests/Entity/UserTest.php`
- `tests/Entity/FormationTest.php`
- `tests/Entity/WorksTest.php`
- `tests/Entity/CommentTest.php`
- `tests/Entity/RatingTest.php`
- `tests/Entity/InscriptionTest.php`
- `tests/Entity/ContactMessageTest.php`
- `tests/Entity/PageTest.php`
- `tests/Entity/PartenaireTest.php`

## Résumé
88 tests, 279 assertions — 100% vert (PHPUnit 13, PHP 8.5).

### Couverture par entité
| Entité | Tests | Aspects couverts |
|--------|-------|-----------------|
| User | 16 | valeurs défaut, collections, roles, eraseCredentials, getUserIdentifier, relations bidirectionnelles |
| Formation | 10 | valeurs défaut, PrePersist, relations bidirectionnelles (responsables/works/inscriptions) |
| Works | 9 | valeurs défaut, PrePersist, relations bidirectionnelles (comment/user/rating) |
| Comment | 11 | valeurs défaut, PrePersist, truncature __toString, relations bidirectionnelles |
| Rating | 10 | valeurs défaut, PrePersist, __toString, relations bidirectionnelles |
| Inscription | 8 | valeurs défaut, PrePersist, treatBy nullable |
| ContactMessage | 7 | valeurs défaut, PrePersist, isRead, readBy nullable |
| Page | 8 | valeurs défaut, PrePersist, relations bidirectionnelles |
| Partenaire | 7 | valeurs défaut, isActive, logo/url nullable |

## Résultat
OK (88 tests, 279 assertions) en 73ms
