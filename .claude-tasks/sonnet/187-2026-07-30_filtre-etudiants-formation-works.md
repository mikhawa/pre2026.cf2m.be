# Tâche 187 — Filtrage des étudiants par formation sur l'édition d'un Work

**Modèle** : Sonnet
**Justification** : Modification d'un controller métier (EasyAdmin CrudController) avec logique de requête Doctrine — hors périmètre CRUD simple (Haiku), sans enjeu d'architecture ou de sécurité majeur (Opus).

**Date** : 2026-07-30

## Contexte
Sur la page d'édition d'un Work (ex. `/admin/works/821/edit`), le champ « Étudiants » proposait tous les utilisateurs ayant `ROLE_STAGIAIRE`, sans restriction liée à la Formation à laquelle appartient le Work. Un formateur/admin pouvait donc associer un étudiant n'ayant aucun lien avec la formation du Work en cours.

## Fichiers modifiés
- `src/Controller/Admin/WorksCrudController.php`

## Résumé
Le `setQueryBuilder` du champ `AssociationField::new('users', 'Étudiants')` restreint désormais la liste aux utilisateurs ayant `ROLE_STAGIAIRE` **et** une entrée `FormationStagiaire` pour la Formation du Work en cours d'édition (jointure arbitraire DQL sur `FormationStagiaire::class` via `fs.user = entity AND fs.formation = :formation`).

La Formation courante est récupérée via `$this->getContext()->getEntity()->getInstance()` (l'instance de Works en cours d'édition). Si le Work n'a pas encore de formation associée (cas de création), le filtre par formation ne s'applique pas et le comportement précédent (tous les stagiaires) est conservé.

## Validation
- `doctrine:schema:validate --skip-sync` : mapping OK
- Requête DQL équivalente testée manuellement sur la formation #455 (Work #811) : résultat conforme à la table `formation_stagiaire`
- `php bin/phpunit` : 177 tests, 404 assertions, OK
- `php-cs-fixer --dry-run` : aucune correction nécessaire