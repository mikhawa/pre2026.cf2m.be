# 041 — Fixtures : dates cohérentes par entité

**Date** : 2026-03-08 12:30
**Branche** : Navigation

## Fichiers modifiés
- `src/Entity/Formation.php`, `Works.php`, `Page.php`
- `src/Factory/FormationFactory.php`, `WorksFactory.php`, `PageFactory.php`

## Problème
`#[ORM\PrePersist]` écrasait toujours `createdAt` avec `new \DateTimeImmutable()`, ignorant la valeur définie par les factories.

## Solution

### Entités
- `setCreatedAtValue()` : ajout de `if ($this->createdAt === null)` pour ne pas écraser une valeur déjà définie
- Ajout de `setCreatedAt(\DateTimeImmutable): static` sur les trois entités

### Factories — plages de dates

| Entité    | `createdAt`           | `publishedAt` si published        |
|-----------|-----------------------|-----------------------------------|
| Formation | -3 ans → -1 an        | -11 mois → -3 mois                |
| Works     | -2 mois → aujourd'hui | après `createdAt` → aujourd'hui   |
| Page      | -3 ans → -1 an        | -3 mois → aujourd'hui             |

### Works `publie()` state
Utilise `afterInstantiate` pour lire le `createdAt` effectivement assigné à l'objet, puis génère `publishedAt` dans l'intervalle `[createdAt, now]`, garantissant la cohérence chronologique.
