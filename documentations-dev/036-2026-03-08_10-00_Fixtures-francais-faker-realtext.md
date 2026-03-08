# 036 — Fixtures en français : remplacement du Lorem Ipsum

**Date** : 2026-03-08 10:00
**Branche** : Navigation

## Fichiers modifiés
- `src/Factory/FormationFactory.php`
- `src/Factory/WorksFactory.php`
- `src/Factory/PageFactory.php`
- `src/Factory/CommentFactory.php`
- `src/Factory/ContactMessageFactory.php`
- `src/Factory/PartenaireFactory.php`
- `src/Factory/UserFactory.php`

## Résumé des changements

### Problème
Malgré la locale `fr_BE` configurée dans `zenstruck_foundry.yaml`, les méthodes `faker()->sentence()`, `faker()->paragraph()` et `faker()->paragraphs()` génèrent du Lorem Ipsum latin car elles utilisent le provider interne de Faker, indépendant de la locale.

### Solution
| Ancienne méthode | Nouvelle méthode |
|---|---|
| `faker()->sentence(n)` | Tableau `const` de titres français |
| `faker()->words(n, true)` | Tableau `const` de titres français |
| `faker()->paragraph()` | `faker()->realText(200)` |
| `faker()->paragraphs(n, true)` | `faker()->realText(500)` |
| `faker()->company()` | Tableau `const` de noms de partenaires belges |

### `realText()`
Génère du texte extrait d'une œuvre littéraire française (via la locale `fr_BE`), produisant un rendu lisible et grammaticalement cohérent en français.

### Tableaux contextuels
Chaque factory possède une constante `TITRES` (ou `NOMS`, `SUJETS`) avec des données réalistes propres au domaine CF2m (multimédia, design, développement web, audiovisuel).

## Raison
Améliorer la lisibilité des fixtures lors des démonstrations, revues de code et tests, et avoir des données cohérentes avec le contexte du centre de formation.
