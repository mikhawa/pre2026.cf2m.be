# 032 - Fixtures : dates cohérentes par entité

**Modèle** : Sonnet
**Justification** : Modification entités + factories
**Date** : 2026-03-08

## Fichiers modifiés
- `src/Entity/Formation.php` — PrePersist conditionnel + setCreatedAt()
- `src/Entity/Works.php` — PrePersist conditionnel + setCreatedAt()
- `src/Entity/Page.php` — PrePersist conditionnel + setCreatedAt()
- `src/Factory/FormationFactory.php`
- `src/Factory/WorksFactory.php`
- `src/Factory/PageFactory.php`

## Résumé

### Entités
`setCreatedAtValue()` modifié : ne set `createdAt` que si null (permet aux factories de le définir).
Setter `setCreatedAt()` ajouté sur les trois entités.

### Plages de dates
| Entité    | createdAt            | publishedAt (si published)       |
|-----------|----------------------|----------------------------------|
| Formation | -3 ans → -1 an       | -11 mois → -3 mois               |
| Works     | -2 mois → aujourd'hui | après createdAt → aujourd'hui   |
| Page      | -3 ans → -1 an       | -3 mois → aujourd'hui            |

### Works publie() state
Utilise `afterInstantiate` pour lire le `createdAt` réel de l'objet et garantir que `publishedAt > createdAt`.
