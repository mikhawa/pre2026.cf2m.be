# 024 — Fixtures : utilisateur Mikhawa ROLE_SUPER_ADMIN

**Date** : 2026-03-05 11:00
**Branche** : FirstFrontend

## Fichiers modifiés

| Fichier | Changement |
|---------|-----------|
| `src/DataFixtures/AppFixtures.php` | Ajout de `UserFactory::createOne()` pour Mikhawa en tête de fixture |
| `src/Factory/UserFactory.php` | `afterInstantiate` lit `plainPassword` avant de hasher |
| `src/Story/AppStory.php` | Mise à jour de cohérence (non chargé par le loader actuel) |

## Raison

Installation des fixtures de développement avec un super administrateur nommé **Mikhawa** permettant de se connecter et de tester l'interface.

## Résumé des changements

### `UserFactory.php`
Modification de la méthode `initialize()` :
- Avant : hachage systématique avec `'password'`
- Après : utilise `$user->getPlainPassword() ?? 'password'`, permettant de passer un mot de passe personnalisé via `createOne(['plainPassword' => '...'])`

### `AppFixtures.php`
Ajout en tête de la méthode `load()` :
```php
UserFactory::createOne([
    'email'         => 'mikhawa@cf2m.be',
    'userName'      => 'Mikhawa',
    'roles'         => ['ROLE_SUPER_ADMIN'],
    'status'        => 1,
    'plainPassword' => '123mikhawa',
]);
```

## Résultat en base

```
id=33 | email=mikhawa@cf2m.be | userName=Mikhawa | roles=["ROLE_SUPER_ADMIN"]
```

Total fixtures insérées : 33 users, 10 formations, 32 inscriptions + partenaires, pages, works, commentaires, ratings, messages de contact.
