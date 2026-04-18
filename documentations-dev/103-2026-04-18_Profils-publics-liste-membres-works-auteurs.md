# 103 — Profils publics, liste membres, liens auteurs Works, restrictions formulaires

**Date** : 2026-04-18  
**Branche** : main

## Fichiers créés

- `src/Controller/PublicProfileController.php`
- `templates/profil/public.html.twig`
- `templates/profil/utilisateurs.html.twig`

## Fichiers modifiés

- `src/Controller/ProfileController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `src/Controller/Admin/CommentCrudController.php`
- `src/Repository/UserRepository.php`
- `templates/profil/index.html.twig`
- `templates/works/show.html.twig`
- `assets/styles/app.css`

## Résumé des changements

### 1. Page de profil public

Nouvelle route publique `GET /utilisateur/{id}` (`app_public_profile`) sans authentification requise. Affiche : nom, avatar (ou placeholder SVG), badge du rôle principal, date d'inscription, biographie et liens externes. Aucune information sensible (pas d'email).

### 2. Page liste des membres

Nouvelle route `GET /profil/utilisateurs` (`app_profile_users`), protégée par `#[IsGranted('ROLE_FORMATEUR')]`. Affiche tous les utilisateurs en grille de cartes (avatar + nom + **tous leurs badges de rôle**), chaque carte cliquable vers le profil public. Un message indique que la page est réservée au personnel du CF2m.

Un bouton « Membres » conditionnel (`is_granted('ROLE_FORMATEUR')`) est présent sur :
- `templates/profil/index.html.twig` (Mon profil)
- `templates/profil/public.html.twig` (profil public de chaque utilisateur)

### 3. Liens auteurs dans la page Works

Dans `templates/works/show.html.twig`, chaque auteur de la sidebar est maintenant un lien cliquable vers son profil public. Nouvelle classe CSS `.cf2m-work-author-link` avec hover opacity, variante light mode incluse.

### 4. Champs non modifiables en édition de commentaire

Dans `CommentCrudController`, les champs `user` et `works` sont désactivés (`disabled`) en page d'édition — l'auteur et le work lié à un commentaire existant ne peuvent plus être changés.

### 5. Couleur badge Stagiaire

Le badge `ROLE_STAGIAIRE` passe de `bg-secondary` à `bg-success` (vert) dans les templates de profil public et de liste membres.

## Raison

Permettre la consultation des profils des membres du site, faciliter la navigation entre auteurs et leurs profils, et renforcer la cohérence des formulaires d'administration (champs immuables en édition).
