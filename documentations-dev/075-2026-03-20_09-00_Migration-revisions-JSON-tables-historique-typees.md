# 075 — Migration des révisions JSON vers les tables d'historique typées

**Date** : 2026-03-20 09:00
**Tâche** : 085 (Phase 2 de la refactorisation historique)

## Fichier créé
- `src/Command/MigrateRevisionsCommand.php`

## Résumé
Commande `bin/console app:migrate-revisions` qui migre les 26 révisions de la table JSON polymorphique `revision` vers les 3 tables typées créées en Phase 1.

**Options disponibles :**
- `--dry-run` : simulation sans écriture en base
- `--force` : vide les tables et relance (idempotent)

**Résultat de la migration :**
- `formation_history` : 11 entrées
- `page_history` : 3 entrées
- `works_history` : 12 entrées
- Ignorées : 0

## Raison
Phase 2 du remplacement de la table `revision` polymorphique par des tables d'historique typées.
