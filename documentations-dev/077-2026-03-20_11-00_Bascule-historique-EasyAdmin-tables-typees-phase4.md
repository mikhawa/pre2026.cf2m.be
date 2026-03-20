# 077 — Bascule historique EasyAdmin vers les tables typées (Phase 4)

**Date** : 2026-03-20 11:00
**Tâche** : 087

## Fichiers modifiés
- `src/Service/RevisionService.php` — +9 méthodes publiques (snapshots, diff, approve/reject)
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `templates/admin/formation/historique.html.twig`
- `templates/admin/page/historique.html.twig`
- `templates/admin/works/historique.html.twig`

## Résumé
Les pages d'historique EasyAdmin (Formation, Page, Works) lisent désormais depuis les nouvelles tables typées (`formation_history`, `page_history`, `works_history`). Les diffs sont calculés par comparaison de versions consécutives. Les actions Approuver/Rejeter fonctionnent sur les nouvelles entités typées avec bridge de notification vers l'ancienne table `revision`.

## Raison
Phase 4 du remplacement de la table `revision` polymorphique. Après cette phase, seule la Phase 5 (`DROP TABLE revision`) reste à faire.
