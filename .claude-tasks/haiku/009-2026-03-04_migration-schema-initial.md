# 009 — Migration initiale du schéma BDD

**Modèle** : Haiku
**Justification** : Génération de migration (tâche CLI simple)
**Date** : 2026-03-04

## Fichier créé
- `migrations/Version20260304112351.php`

## Résumé
Migration générée via `doctrine:migrations:diff`.
Crée l'intégralité du schéma initial : 9 tables métier + 5 tables de jointure + messenger_messages.

Tables créées : `user`, `formation`, `works`, `comment`, `rating`, `inscription`,
`contact_message`, `page`, `partenaire`, `formation_user`, `works_user`,
`page_user`, `comment_rating`, `rating_works`, `messenger_messages`.

## Résultat
Migration prête à exécuter via `doctrine:migrations:migrate`.
