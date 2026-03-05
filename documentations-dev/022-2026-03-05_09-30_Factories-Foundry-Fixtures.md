# 022 — Factories Foundry et Fixtures de développement

**Date** : 2026-03-05 09:30
**Auteur** : Claude Sonnet (tâche 012)

## Fichiers modifiés

| Fichier | Action |
|---------|--------|
| `config/packages/zenstruck_foundry.yaml` | Ajout `faker.locale: fr_BE` |
| `src/Factory/UserFactory.php` | Personnalisé avec hachage MDP + states |
| `src/Factory/FormationFactory.php` | Callable defaults + states |
| `src/Factory/WorksFactory.php` | Callable defaults + state |
| `src/Factory/CommentFactory.php` | Données sémantiques + state |
| `src/Factory/RatingFactory.php` | Valeur 1-5 |
| `src/Factory/InscriptionFactory.php` | Données sémantiques + state |
| `src/Factory/ContactMessageFactory.php` | Données sémantiques + state |
| `src/Factory/PartenaireFactory.php` | Données sémantiques + state |
| `src/DataFixtures/AppFixtures.php` | Fixtures complètes pour les 9 entités |

## Résumé

Personnalisation des 9 factories générées par `make:factory` avec des données Faker sémantiquement appropriées en locale `fr_BE`. Chaque factory expose des états (states) métier pour faciliter la création de jeux de données ciblés.

`AppFixtures.php` crée un jeu de données réaliste :
- 32 utilisateurs (admins, formateurs, étudiants)
- 10 formations avec responsables
- 32 travaux avec auteurs
- 49 commentaires, 45 notations
- 27 inscriptions (traitées + en attente)
- 12 messages de contact, 6 pages CMS, 8 partenaires

## Raison

Permettre le développement et les tests manuels avec des données réalistes sans avoir à saisir manuellement des enregistrements en base.

## Conventions respectées

- Locale `fr_BE` pour les données Faker
- Slugs dérivés des titres (pas de texte Lorem generique)
- Clés factory alignées sur les setters Doctrine (`active`, `approved`, `read` sans préfixe `is`)
- Foundry 2.x : pas de `->_real()` (les entités sont directement retournées)
