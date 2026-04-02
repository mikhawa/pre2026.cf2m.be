---
modele: opus
justification: Architecture majeure -- creation de 3 tables d'historique typees en remplacement du systeme JSON polymorphique de la table `revision`
---

## Tache 084 -- Tables d'historique typees Formation / Page / Works

### Fichiers crees
- `src/Entity/Trait/RevisionWorkflowTrait.php`
- `src/Entity/FormationHistory.php`
- `src/Entity/PageHistory.php`
- `src/Entity/WorksHistory.php`
- `src/Repository/FormationHistoryRepository.php`
- `src/Repository/PageHistoryRepository.php`
- `src/Repository/WorksHistoryRepository.php`
- `migrations/Version20260320113106.php` (generee par Doctrine)

### Resume
Creation des entites d'historique avec colonnes typees (pas de JSON), numerotation de version sequentielle par entite, workflow de validation integre (pending/approved/rejected/auto-approved), tables de jointure ManyToMany historiques, et methode factory `fromEntite()` pour le snapshot. Cette phase remplace la structure JSON de la table `revision` par des colonnes SQL typees et interrogeables.
