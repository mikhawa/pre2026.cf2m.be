# 018 — Corrections Formation, User et database-schema

**Date** : 2026-03-04 10:10
**Modèle** : Sonnet

## Fichiers modifiés

- `src/Entity/Formation.php`
- `src/Entity/User.php`
- `docs/architecture/database-schema.md`

## Résumé des changements

### Formation.php
- Ajout de `$createdBy` (ManyToOne → User, JoinColumn nullable: false) — créateur obligatoire
- Ajout de `$updatedAt` (DateTimeImmutable nullable)
- Ajout de `$updatedBy` (ManyToOne → User, JoinColumn nullable) — dernier modificateur
- Getters/setters correspondants

### User.php
- `biography` : varchar(500) → varchar(600), Assert\Length max 500 → 600

### database-schema.md
- Formation : description "stagiaires" → "responsables", ajout colonnes `created_by_user_id`, `updated_at`, `updated_by_user_id`, mise en forme
- Inscription : correction des espaces parasites avant `|`
- Rating : "Message" → "Comment"
- Tables de jointure : `rating_messages` → `comment_rating`, ordre alphabétique, précision "responsables"
- Relations résumées : notation unifiée `──<` / `>──<`, remplacement "stagiaires" → "responsables"

## Raison
Corrections des incohérences identifiées entre l'entité `Formation` créée et le schéma BDD :
champs manquants (`createdBy`, `updatedAt`, `updatedBy`), sémantique ManyToMany (responsables ≠ stagiaires),
`Comment` remplace `Messages`, `biography` 600 chars confirmé.
