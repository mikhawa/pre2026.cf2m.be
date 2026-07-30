# 122 — Filtrage dynamique des étudiants par formation à la création d'un Work

**Date :** 2026-07-30
**Commit :** non commité
**Branche :** `feature/26-update-stagiaires-to-admin`

---

## Contexte

Suite à `documentations-dev/121-2026-07-30_Filtre-etudiants-formation-works.md`, le champ « Étudiants » du formulaire `Works` était filtré par formation **uniquement à l'édition** : le `queryBuilder` server-side se base sur la formation déjà persistée sur l'entité (`$this->getContext()->getEntity()->getInstance()->getFormation()`), donc inopérant à la création puisque le formulaire `new` n'a pas encore de formation choisie au moment du rendu initial de la page.

Résultat : sur `/admin/works/new`, le champ « Étudiants » proposait tous les stagiaires de la plateforme, sans lien avec la formation qu'on s'apprêtait à choisir juste au-dessus dans le même formulaire.

---

## Fonctionnalité ajoutée

### Endpoint AJAX

`GET /admin/works/etudiants-formation/{formationId}` (`WorksCrudController::etudiantsFormation()`), déclaré via `#[AdminRoute]` :
- retourne un JSON `[{id, text}, ...]` des stagiaires de la formation demandée (`FormationStagiaireRepository::findForFormation()`) ;
- accès réservé à `ROLE_FORMATEUR` ;
- pour un formateur non-admin, restreint aux formations dont il est responsable (même règle que le `queryBuilder` du champ `formation`) — sinon accès refusé.

### Script front

`assets/works_etudiants_filter.js` (importé dans `admin.js`, pas d'entrée `importmap.php` nécessaire — import relatif résolu par AssetMapper) :
- écoute l'événement `change` sur le `<select>` `Works_formation` par délégation sur `document` (survit aux navigations Turbo, même pattern que `comment_approve.js`) ;
- appelle l'endpoint et remplace les `<option>` du `<select multiple>` `Works_users` par la liste reçue ;
- préserve les sélections déjà cochées si elles restent présentes dans la nouvelle liste ;
- vide la liste si aucune formation n'est sélectionnée.

Comme le script réagit à **tout** changement du champ formation, il s'applique aussi bien à la création qu'à l'édition (complète le filtrage server-side déjà en place pour ce dernier cas).

### Champ ciblé

Les champs Symfony/EasyAdmin du formulaire `Works` suivent le pattern d'id `{EntityShortName}_{propriété}` (déjà utilisé par `initSlugSync()` dans `admin.js`) : `Works_formation` et `Works_users`. Le widget `AssociationField` par défaut (sans `->autocomplete()`) est un `<select>` natif Symfony, sans librairie JS de type TomSelect/Select2 — la manipulation directe des `<option>` en JS est donc suffisante, sans API tierce à réinitialiser.

---

## Fichiers modifiés / créés

- `src/Controller/Admin/WorksCrudController.php` (action `etudiantsFormation`, imports `FormationRepository`, `FormationStagiaireRepository`, `JsonResponse`)
- `assets/works_etudiants_filter.js` (créé)
- `assets/admin.js` (import du nouveau script)

---

## Vérifications effectuées

- `doctrine:schema:validate --skip-sync` → mapping correct
- `debug:router` → route `/admin/works/etudiants-formation/{formationId}` enregistrée, sans collision avec les routes CRUD existantes
- `lint:container` → OK
- Requête DQL équivalente à `findForFormation()` exécutée manuellement sur la formation #455 : résultat conforme (6 stagiaires attendus)
- `bin/phpunit` → 177 tests, 404 assertions, aucune régression
- `php-cs-fixer --dry-run` → aucune correction nécessaire
- **Non vérifié** : test manuel en navigateur (connexion en CLI bloquée par Cloudflare Turnstile + double authentification) — à confirmer visuellement sur `/admin/works/new`

---

## Traçabilité

Implémenté par le modèle **Sonnet**. Détails complets : `.claude-tasks/sonnet/188-2026-07-30_filtre-etudiants-formation-creation-works.md`.