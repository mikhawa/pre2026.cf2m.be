# 021 — Migration corrective : TINYINT UNSIGNED + DEFAULT CURRENT_TIMESTAMP

**Date** : 2026-03-04 11:40
**Modèle** : Sonnet

## Contexte

La migration initiale (Version20260304112351) avait été appliquée en BDD avant que les modifications
suivantes y soient intégrées :
- Les `TINYINT` booléens → `TINYINT UNSIGNED`
- Les `created_at` → `DEFAULT CURRENT_TIMESTAMP`

Doctrine ne détecte pas les différences TINYINT signé/unsigned, d'où l'absence de diff automatique.
Une migration corrective manuelle a été nécessaire.

## Fichiers créés

### `migrations/Version20260304114038.php`
Migration manuelle avec `ALTER TABLE … MODIFY` pour :
- 4 colonnes booléennes → `TINYINT UNSIGNED`
- 8 colonnes `created_at` → `DEFAULT CURRENT_TIMESTAMP`

## Fichiers modifiés

| Fichier | Modification |
|---------|--------------|
| `src/Entity/Comment.php` | `isApproved` : `'unsigned' => true` |
| `src/Entity/ContactMessage.php` | `isRead` : `'unsigned' => true` |
| `src/Entity/Inscription.php` | `treat` : `'unsigned' => true` |
| `src/Entity/Partenaire.php` | `isActive` : `'unsigned' => true` |

## Détail de la migration

### TINYINT UNSIGNED
```sql
ALTER TABLE comment MODIFY is_approved TINYINT UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE contact_message MODIFY is_read TINYINT UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE inscription MODIFY treat TINYINT UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE partenaire MODIFY is_active TINYINT UNSIGNED DEFAULT 0 NOT NULL;
```

### DEFAULT CURRENT_TIMESTAMP
```sql
ALTER TABLE comment MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE contact_message MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE formation MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE inscription MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE page MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE rating MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `user` MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE works MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
```

> Note : `partenaire` n'a pas de colonne `created_at` (aucun timestamp sur cette entité).

## Résultat vérifié en BDD
- `is_approved` : `tinyint(3) unsigned` ✅
- `created_at` : `DEFAULT current_timestamp()` ✅

## État des migrations
| Version | Description | Statut |
|---------|-------------|--------|
| Version20260304112351 | Création initiale du schéma | ✅ Appliquée |
| Version20260304114038 | Correctif TINYINT UNSIGNED + CURRENT_TIMESTAMP | ✅ Appliquée |
