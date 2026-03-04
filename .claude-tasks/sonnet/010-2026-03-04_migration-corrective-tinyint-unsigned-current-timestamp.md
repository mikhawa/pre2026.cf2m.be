# 010 — Migration corrective TINYINT UNSIGNED + DEFAULT CURRENT_TIMESTAMP

**Modèle** : Sonnet
**Justification** : Diagnostic BDD + création de migration manuelle (raisonnement nécessaire)
**Date** : 2026-03-04

## Contexte
La première migration (Version20260304112351) avait été appliquée AVANT que nous y ajoutions
les modifications TINYINT UNSIGNED et DEFAULT CURRENT_TIMESTAMP. La BDD était donc en retard.
Doctrine ne détectait pas ces écarts (TINYINT signé vs unsigned non différencié par Doctrine).

## Fichiers créés/modifiés
- `migrations/Version20260304114038.php` — migration corrective manuelle
- `src/Entity/Comment.php` — `isApproved` : ajout `'unsigned' => true`
- `src/Entity/ContactMessage.php` — `isRead` : ajout `'unsigned' => true`
- `src/Entity/Inscription.php` — `treat` : ajout `'unsigned' => true`
- `src/Entity/Partenaire.php` — `isActive` : ajout `'unsigned' => true`

## Résumé

### TINYINT UNSIGNED (4 colonnes)
- `comment.is_approved`
- `contact_message.is_read`
- `inscription.treat`
- `partenaire.is_active`

### DEFAULT CURRENT_TIMESTAMP (8 tables)
`comment`, `contact_message`, `formation`, `inscription`, `page`, `rating`, `user`, `works`

Note : `partenaire` n'a pas de colonne `created_at` (entité sans timestamp).

## Résultat
Migration appliquée avec succès. Vérification BDD : `tinyint(3) unsigned` + `DEFAULT current_timestamp()` confirmés.
