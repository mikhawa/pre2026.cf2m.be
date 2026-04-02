# Décision architecturale — Système d'historique des révisions

**Date** : 2026-03-20
**Statut** : Adopté (migration terminée — tâches 084 à 090)
**Auteur** : Mikhawa / Claude Sonnet

---

## Contexte

Le projet nécessite de tracer toutes les modifications apportées aux entités de contenu (`Formation`, `Page`, `Works`) afin de :
- Permettre un workflow de validation (formateur soumet → admin approuve/rejette)
- Conserver un historique complet des versions
- Pouvoir restaurer une version antérieure
- Visualiser les différences entre deux versions (diff)

Deux approches ont été évaluées, puis la première a été implémentée avant d'être remplacée par la seconde.

---

## Approche 1 — Table polymorphique avec données JSON *(abandonnée)*

### Schéma
```sql
CREATE TABLE revision (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    entity_type     VARCHAR(50)  NOT NULL,          -- 'formation', 'page', 'works'
    entity_id       INT UNSIGNED NOT NULL,
    entity_title    VARCHAR(255),
    data            LONGTEXT,                        -- JSON snapshot "après"
    previous_data   LONGTEXT,                        -- JSON snapshot "avant"
    status          SMALLINT UNSIGNED DEFAULT 0,
    created_by_id   INT UNSIGNED,
    reviewed_by_id  INT UNSIGNED,
    created_at      DATETIME,
    reviewed_at     DATETIME,
    FOREIGN KEY (created_by_id) REFERENCES user(id),
    FOREIGN KEY (reviewed_by_id) REFERENCES user(id)
);
```

### Pour
- Une seule table, une seule entité Doctrine, un seul repository
- Ajouter un nouveau type de contenu ne nécessite aucune migration
- Requête "toutes révisions en attente" triviale (une seule `SELECT`)
- Code service compact

### Contre
- **Pas de contrainte FK sur le contenu** : si une Formation est supprimée, ses révisions deviennent orphelines silencieusement
- **Le JSON est opaque** : impossible d'indexer ou de requêter efficacement sur `data->>'$.title'` → full table scan systématique
- **ManyToMany non capturables** : les responsables / participants ne peuvent pas être sérialisés dans le JSON sans logique de reconstruction complexe (c'était le bug principal qui a motivé la migration)
- **Duplication de données** : chaque révision stocke deux blobs complets (`data` + `previous_data`) — pour une description HTML de 50 Ko, cela représente 100 Ko par révision
- Schéma "fourre-tout" : ajouter un champ à `Formation` modifie silencieusement le contenu JSON sans migration contrôlée
- Diff réalisé entièrement en PHP (décodage + comparaison) — le moteur SQL ne peut pas contribuer

---

## Approche 2 — Tables typées par entité *(adoptée)*

### Schéma
```sql
-- Formation
CREATE TABLE formation_history (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    formation_id     INT UNSIGNED NOT NULL,
    version          INT UNSIGNED NOT NULL,
    title            VARCHAR(255),
    slug             VARCHAR(255),
    description      LONGTEXT,
    status           VARCHAR(50),
    color_primary    VARCHAR(7),
    color_secondary  VARCHAR(7),
    published_at     DATETIME,
    revision_status  SMALLINT UNSIGNED DEFAULT 0,  -- 0=pending 1=approved 2=rejected 3=auto
    created_by_id    INT UNSIGNED,
    reviewed_by_id   INT UNSIGNED,
    created_at       DATETIME,
    reviewed_at      DATETIME,
    UNIQUE KEY (formation_id, version),
    FOREIGN KEY (formation_id)   REFERENCES formation(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_id)  REFERENCES user(id),
    FOREIGN KEY (reviewed_by_id) REFERENCES user(id)
);

-- Table de jointure ManyToMany
CREATE TABLE formation_history_responsable (
    formation_history_id INT UNSIGNED NOT NULL,
    user_id              INT UNSIGNED NOT NULL,
    PRIMARY KEY (formation_history_id, user_id)
);

-- Idem pour page_history + page_history_user
-- Idem pour works_history + works_history_user
```

### Pour
- **Intégrité référentielle réelle** : `CASCADE DELETE` sur `formation_id` — pas d'orphelins
- **ManyToMany nativement stockés** dans des tables de jointure dédiées
- **Chaque champ est indexable** : recherche sur `title`, `status`, `version` en O(log n)
- Diff réalisable en SQL pur (jointure sur `version - 1`)
- Contrainte `UNIQUE (formation_id, version)` garantit l'unicité des versions au niveau moteur
- Snapshot immuable et structurellement validé par le schéma
- Migrations Doctrine contrôlées : ajouter un champ = migration explicite

### Contre
- 6 tables supplémentaires (3 history + 3 jointures ManyToMany)
- Ajouter un nouveau type de contenu nécessite une nouvelle entité + repository + migrations + méthodes dans `RevisionService`
- `RevisionService` est plus volumineux (méthodes dupliquées × 3 types)
- Migration initiale des données coûteuse (tâche 085 — commande `MigrateRevisionsCommand`)

---

## Comparaison SQL

### Requête "révisions en attente"

**JSON polymorphique**
```sql
SELECT * FROM revision WHERE status = 0 ORDER BY created_at DESC;
-- 1 table, 1 index sur status → O(log n)
```

**Tables typées**
```sql
SELECT * FROM formation_history WHERE revision_status = 0
UNION ALL
SELECT * FROM page_history      WHERE revision_status = 0
UNION ALL
SELECT * FROM works_history     WHERE revision_status = 0
ORDER BY created_at DESC;
-- 3 tables, 3 index → toujours O(log n) par table
```

### Diff entre deux versions

**JSON polymorphique**
```sql
-- Impossible en SQL : récupération des deux blobs, diff en PHP
SELECT data, previous_data FROM revision WHERE id = 42;
```

**Tables typées**
```sql
SELECT h1.title AS title_apres, h2.title AS title_avant,
       h1.description AS desc_apres, h2.description AS desc_avant
FROM formation_history h1
JOIN formation_history h2
  ON h1.formation_id = h2.formation_id AND h2.version = h1.version - 1
WHERE h1.id = 42;
-- Index UNIQUE (formation_id, version) utilisé → très rapide
```

### Recherche "toutes révisions ayant changé le titre"

**JSON polymorphique**
```sql
SELECT * FROM revision
WHERE JSON_EXTRACT(data, '$.title') != JSON_EXTRACT(previous_data, '$.title');
-- Full table scan garanti (MariaDB n'indexe pas JSON_EXTRACT sans colonne générée)
```

**Tables typées**
```sql
SELECT h1.* FROM formation_history h1
JOIN formation_history h2
  ON h1.formation_id = h2.formation_id AND h2.version = h1.version - 1
WHERE h1.title != h2.title;
-- Index B-tree utilisé
```

---

## Comparaison taille base de données

| Scénario | JSON polymorphique | Tables typées |
|---|---|---|
| 1 révision, description 10 Ko | ~20 Ko (data + previous_data) | ~10 Ko (1 champ) |
| 100 révisions, description 10 Ko | ~2 Mo | ~1 Mo |
| 1000 révisions, description 50 Ko | ~100 Mo | ~50 Mo |
| Relations ManyToMany (responsables) | Sérialisées en JSON (poids variable) | Entiers (8 octets/ligne) |

La duplication `data`/`previous_data` dans l'approche JSON double mécaniquement le volume de stockage dès que les champs HTML sont conséquents.

---

## Conclusion

**Les tables typées ont été retenues** pour les raisons suivantes, par ordre d'importance :

1. **Intégrité des données** : contraintes FK réelles, pas d'orphelins silencieux
2. **ManyToMany** : impossible à capturer fidèlement en JSON sans logique de reconstruction (bug avéré en production)
3. **Performance à l'échelle** : index B-tree sur tous les champs, diff SQL possible
4. **Taille** : stockage ~2× plus compact pour les contenus HTML volumineux

Le surcoût (6 tables, `RevisionService` plus lourd) est acceptable pour un nombre fixe de 3 types de contenu. Si le projet devait gérer 10+ types de contenu avec des cycles courts d'ajout, l'approche JSON serait plus pragmatique.

### Statuts `revision_status`
| Valeur | Constante | Signification |
|---|---|---|
| 0 | `STATUS_PENDING` | En attente de validation admin |
| 1 | `STATUS_APPROVED` | Approuvé après relecture |
| 2 | `STATUS_REJECTED` | Rejeté par un admin |
| 3 | `STATUS_AUTO_APPROVED` | Auto-approuvé (admin direct ou restauration) |

---

*Voir aussi : `datas/UML-db/schema-database.md` pour le schéma complet de la base.*
