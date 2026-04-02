# Tâche 085 — Migration des révisions JSON vers les tables d'historique typées (Phase 2)

**Modèle** : Sonnet
**Justification** : Commande Symfony métier avec mapping de données et logique de migration

## Fichiers créés
- `src/Command/MigrateRevisionsCommand.php`

## Résumé
Création de `app:migrate-revisions`, commande Symfony qui lit toutes les lignes de la table `revision` (JSON polymorphique) et les insère dans les 3 tables typées :
- `formation_history` (11 entrées)
- `page_history` (3 entrées)
- `works_history` (12 entrées)

## Fonctionnalités
- Option `--dry-run` : simulation sans écriture
- Option `--force` : vide les tables avant de relancer (idempotent)
- Numérotation séquentielle de version par entité (triée par `created_at ASC, id ASC`)
- Mapping status 0/1/2 → identique dans `revision_status`
- ManyToMany (responsables/users) depuis l'état live de l'entité (limitation connue)
- Formation liée dans WorksHistory : depuis le JSON (`formationId`) en priorité, sinon depuis l'état live
- Gestion des entités introuvables (skip + décrémentation de version)

## Résultat
✅ Migration exécutée : 26 révisions migrées, 0 ignorée
