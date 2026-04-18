---
modèle: sonnet
justification: Nouvelle route + template + méthode repository + lien conditionnel
fichiers modifiés:
  - src/Controller/ProfileController.php
  - src/Repository/UserRepository.php
  - templates/profil/index.html.twig
  - assets/styles/app.css
fichiers créés:
  - templates/profil/utilisateurs.html.twig
---

## Résumé

Nouvelle page `/profil/utilisateurs` listant tous les membres du site avec lien vers leur profil public. Accessible uniquement aux `ROLE_FORMATEUR` et au-dessus (via `#[IsGranted('ROLE_FORMATEUR')]`). Un bouton « Membres » apparaît dans la carte profil uniquement pour ces rôles.

## Changements

- `UserRepository::findAllOrderedByName()` : retourne tous les utilisateurs triés par `userName`
- `ProfileController::users()` : route `GET /profil/utilisateurs` (`app_profile_users`), protégée par `ROLE_FORMATEUR`
- `templates/profil/utilisateurs.html.twig` : grille de cartes (avatar + nom + badge de rôle), chaque carte est un lien vers `app_public_profile`
- `templates/profil/index.html.twig` : bouton « Membres » conditionnel (`is_granted('ROLE_FORMATEUR')`)
- `assets/styles/app.css` : `.cf2m-user-card` avec hover opacity + translateY + variante light mode
