# Tâche 188 — Filtrage dynamique des étudiants par formation à la création d'un Work

**Modèle** : Sonnet
**Justification** : Extension d'un controller métier (EasyAdmin CrudController) avec un endpoint AJAX + script JS d'accompagnement — logique applicative sans enjeu d'architecture ou de sécurité majeur.

**Date** : 2026-07-30

## Contexte
La tâche 187 (`.claude-tasks/sonnet/187-...md`) restreignait le champ « Étudiants » aux stagiaires de la formation du Work **à l'édition**, via le `queryBuilder` server-side (basé sur la formation déjà persistée sur l'entité). Cette restriction ne pouvait pas s'appliquer **à la création** d'un Work : au moment du rendu du formulaire `new`, aucune formation n'est encore sélectionnée sur l'entité en mémoire, donc le filtre restait inactif et tous les stagiaires de la plateforme étaient proposés.

## Fonctionnalité ajoutée
- Nouvel endpoint `GET /admin/works/etudiants-formation/{formationId}` (`WorksCrudController::etudiantsFormation()`) qui retourne en JSON les stagiaires (`{id, text}`) d'une formation donnée, via `FormationStagiaireRepository::findForFormation()`. Accès réservé à `ROLE_FORMATEUR`, et restreint aux formations dont l'utilisateur est responsable pour un formateur non-admin (même règle que le champ `formation` du formulaire).
- Script `assets/works_etudiants_filter.js` (importé dans `admin.js`) : écoute le changement du `<select>` `Works_formation` (event delegation sur `document`, survit à Turbo) et recharge dynamiquement les `<option>` du `<select multiple>` `Works_users` via l'endpoint ci-dessus. Les sélections déjà cochées et toujours valides sont préservées.

Cela couvre à la fois la création et l'édition (le script réagit à tout changement de formation sur le formulaire, indépendamment de la page).

## Fichiers modifiés / créés
- `src/Controller/Admin/WorksCrudController.php` (nouvelle action `etudiantsFormation`, imports `FormationRepository`, `FormationStagiaireRepository`, `JsonResponse`)
- `assets/works_etudiants_filter.js` (créé)
- `assets/admin.js` (import du nouveau script)

## Validation
- `doctrine:schema:validate --skip-sync` : mapping OK
- `debug:router` : nouvelle route `/admin/works/etudiants-formation/{formationId}` enregistrée sans collision
- `lint:container` : OK
- Requête DQL équivalente à `findForFormation()` testée manuellement sur la formation #455 : résultat conforme (6 stagiaires)
- `php bin/phpunit` : 177 tests, 404 assertions, OK
- `php-cs-fixer --dry-run` : aucune correction nécessaire
- Test manuel en navigateur non effectué (login bloqué en CLI par Cloudflare Turnstile + 2FA) — à vérifier visuellement sur `/admin/works/new`