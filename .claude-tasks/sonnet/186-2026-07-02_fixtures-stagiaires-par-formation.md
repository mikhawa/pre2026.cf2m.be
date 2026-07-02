# Tâche : Rattachement des stagiaires aux formations dans les fixtures

**Numéro** : 186
**Date** : 2026-07-02
**Modèle utilisé** : Sonnet
**Justification du modèle** : Couche fixtures avec logique de composition (dérivation des membres à partir des auteurs de Works, gestion de l'ordre de flush par rapport au `StagiaireService` déjà en place) — au-delà du simple CRUD, sans toucher à la sécurité/l'architecture (critères `.claude/models.md`).
**Complexité** : Simple
**Fichiers concernés** : `src/DataFixtures/AppFixtures.php`

## Contexte nécessaire
Suite à l'implémentation de l'Option 2 (gestion des stagiaires par formation, tâche `.claude-tasks/opus/185-...`), les fixtures de dev créaient déjà des utilisateurs avec le rôle global `ROLE_STAGIAIRE` (30 stagiaires fakers + `magib@cf2m.be`) mais ne les rattachaient à aucune `Formation` via la nouvelle entité `FormationStagiaire`. L'utilisateur a demandé si les fixtures pouvaient peupler ce rattachement.

## Objectif
Utiliser `StagiaireService::ajouterStagiaire()` pour rattacher les stagiaires existants aux formations créées dans `AppFixtures`, de façon cohérente avec les données déjà générées (auteurs de Works).

## Implémentation
- Injection de `StagiaireService` dans le constructeur de `AppFixtures`.
- Pour chaque formation : les auteurs de ses `Works` (déjà assignés plus haut dans le fichier) sont rattachés comme stagiaires de cette formation, plus 2 à 5 stagiaires supplémentaires (pris dans le pool des 30 fakers) pour simuler des inscrits sans travail encore publié.
- `addedBy` = un responsable de la formation si disponible, sinon `mikhawa` (premier `$usersManuel`).
- Le stagiaire de démonstration `magib@cf2m.be` (variable `$stagiaireDemo`) est explicitement rattaché à la première formation (`Aventure digitale`) pour disposer d'un compte de test facilement identifiable.
- `ProdFixtures.php` non concerné (ne crée ni formations ni stagiaires fakers).

## Vérifications effectuées
- `doctrine:fixtures:load --group=app --no-interaction` : exécution sans erreur.
- Requête DQL de contrôle : 25 lignes `FormationStagiaire` créées, `addedBy` correctement renseigné, `magib@cf2m.be` bien rattaché à « Aventure digitale ».
- Suite complète PHPUnit (`bin/phpunit`) : 177 tests, 404 assertions, OK — aucune régression.
- `php-cs-fixer --dry-run` : le diff complet du fichier reflète un problème pré-existant de fins de ligne CRLF (checkout local), pas une non-conformité introduite ; contenu réel confirmé propre via `git diff -w` (31 lignes ajoutées, cohérentes avec le changement).

## Résultat
Fixtures fonctionnelles : chaque formation a désormais un ensemble réaliste de stagiaires rattachés via `FormationStagiaire`, consultables dans l'écran EasyAdmin « Stagiaires » de `FormationCrudController`.
