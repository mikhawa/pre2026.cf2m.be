# 014 — Fixtures : utilisateur Mikhawa ROLE_SUPER_ADMIN

**Date** : 2026-03-05
**Modèle** : Haiku
**Justification** : Ajout de fixtures simple (CRUD, aucune logique métier complexe)

## Fichiers modifiés

- `src/DataFixtures/AppFixtures.php` — ajout de l'utilisateur Mikhawa en tête de fixture
- `src/Factory/UserFactory.php` — `afterInstantiate` utilise `plainPassword` si défini (sinon `'password'`)
- `src/Story/AppStory.php` — mis à jour (non utilisé par le loader, mais cohérent)

## Résumé

Ajout de l'utilisateur super administrateur **Mikhawa** (`mikhawa@cf2m.be`, `ROLE_SUPER_ADMIN`, mot de passe `123mikhawa`) dans `AppFixtures`.

Modification de `UserFactory::initialize()` pour lire `user->getPlainPassword()` avant de hasher, permettant de passer un mot de passe personnalisé via `createOne(['plainPassword' => '...'])`.

## Résultat

Fixtures chargées avec succès :
- 33 utilisateurs (1 SUPER_ADMIN, 2 ADMIN, 5 FORMATEUR, 25 étudiants)
- 10 formations
- 32 inscriptions
- Partenaires, pages, works, commentaires, ratings, messages de contact

Vérification MariaDB :
```
id=33 | email=mikhawa@cf2m.be | user_name=Mikhawa | roles=["ROLE_SUPER_ADMIN"]
```
