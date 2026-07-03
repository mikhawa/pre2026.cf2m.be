# 120 — Fixtures : rattachement des stagiaires aux formations

**Date :** 2026-07-02
**Commit :** `ecbca2c`
**Branche :** `feature/25-hierarchie-and-classes`

---

## Contexte

Suite à l'introduction de l'entité pivot `FormationStagiaire` (voir doc #119), les fixtures de dev créaient déjà des utilisateurs avec le rôle global `ROLE_STAGIAIRE` (30 stagiaires générés via Faker + le compte de démo `magib@cf2m.be`), mais aucun n'était rattaché à une `Formation` via la nouvelle entité pivot.

---

## Changement

`AppFixtures` reçoit désormais `StagiaireService` par injection de constructeur, utilisé pour peupler `FormationStagiaire` de façon cohérente avec les données déjà générées :

- pour chaque formation, les **auteurs des Works** déjà rattachés à cette formation (plus haut dans le fichier de fixtures) sont ajoutés comme stagiaires ;
- 2 à 5 stagiaires supplémentaires sont piochés aléatoirement dans le pool des 30 fakers, pour simuler des inscrits n'ayant pas encore publié de Work ;
- `addedBy` est renseigné avec un responsable de la formation si disponible, sinon avec `mikhawa` (premier utilisateur du tableau `$usersManuel`) ;
- le compte de démonstration `magib@cf2m.be` est explicitement rattaché à la première formation (« Aventure digitale ») pour disposer d'un compte de test facilement identifiable dans l'écran EasyAdmin « Stagiaires ».

`ProdFixtures.php` n'est pas concerné : il ne crée ni formations ni stagiaires fakers.

---

## Fichiers modifiés

- `src/DataFixtures/AppFixtures.php`

---

## Vérifications effectuées

- `doctrine:fixtures:load --group=app --no-interaction` : exécution sans erreur
- Requête DQL de contrôle : 25 lignes `FormationStagiaire` créées, `addedBy` correctement renseigné, `magib@cf2m.be` bien rattaché à « Aventure digitale »
- `bin/phpunit` (suite complète) : 177 tests, 404 assertions, OK — aucune régression
- `php-cs-fixer --dry-run` : diff résiduel identifié comme un problème préexistant de fins de ligne CRLF (checkout local), pas une non-conformité introduite ; contenu réel confirmé propre via `git diff -w`

---

## Raison

Permet de disposer, dès `make fixtures` (ou équivalent), d'un jeu de données réaliste où chaque formation a un ensemble de stagiaires rattachés, consultable immédiatement dans l'écran EasyAdmin « Stagiaires » ajouté par la doc #119 — sans étape manuelle supplémentaire pour tester la fonctionnalité.

---

## Traçabilité

Implémenté par le modèle **Sonnet** (logique de composition dans la couche fixtures, au-delà du simple CRUD mais sans impact sécurité/architecture). Détails complets : `.claude-tasks/sonnet/186-2026-07-02_fixtures-stagiaires-par-formation.md`.