# 021 — Page de profil + dropdown utilisateur dans la navbar

**Date** : 2026-03-07
**Modèle** : Sonnet (controller, sécurité, templates, CSS)
**Justification** : Controller métier, configuration sécurité, templates, CSS.

## Fichiers créés / modifiés

- `src/Controller/ProfileController.php` — créé : route `GET /profil` (`app_profile`), `#[IsGranted('ROLE_USER')]`
- `templates/profil/index.html.twig` — créé : affiche avatar, username, email, biographie, formations, liens externes
- `templates/base.html.twig` — bloc auth remplacé par dropdown Bootstrap sur le nom d'utilisateur (Mon profil / Administration si ROLE_ADMIN / Déconnexion)
- `assets/styles/app.css` — ajout styles `.cf2m-user-dropdown-toggle`, `.cf2m-avatar-*`, page profil
- `config/packages/security.yaml` — ajout `role_hierarchy` (ROLE_SUPER_ADMIN → ROLE_ADMIN → ROLE_USER) + `access_control` pour `/profil` et `/admin`

## Résumé

Le nom de l'utilisateur connecté dans la navbar est désormais un bouton dropdown Bootstrap avec :
- "Mon profil" → `/profil`
- "Administration" (visible uniquement avec `ROLE_ADMIN`) → `/admin`
- "Déconnexion" → logout

## Résultat

Page profil fonctionnelle, sécurisée par `ROLE_USER`. Le lien Administration s'affiche pour ROLE_ADMIN et ROLE_SUPER_ADMIN (via `role_hierarchy`).
