# 014 — Uniformisation `int unsigned` dans database-schema.md

**Date** : 2026-03-03 16:00
**Fichier modifié** : `docs/architecture/database-schema.md`

## Résumé
Tous les champs de type `int` sans `unsigned` ont été mis à jour pour respecter la convention du projet (`id` et clés étrangères toujours `unsigned`).

## Champs mis à jour
- **Formation** : `id`, `user_id`
- **Works** : `id`, `formation_id`
- **Messages** : `id`, `user_id`, `works_id`
- **Inscription** : `id`, `formation_id`
- **ContactMessage** : `id`
- **Page** : `id`, `user_id`
- **Partenaire** : `id`
- **Comment** : `user_id`
- **Rating** : `user_id`

## Raison
Convention projet : tous les entiers numériques (PK et FK) doivent être `unsigned`.
