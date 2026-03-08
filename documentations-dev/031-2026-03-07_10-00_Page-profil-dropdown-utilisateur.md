# 031 — Page de profil + dropdown utilisateur

**Date** : 2026-03-07 10h00

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/ProfileController.php` | Créé |
| `templates/profil/index.html.twig` | Créé |
| `templates/base.html.twig` | Mise à jour bloc auth |
| `assets/styles/app.css` | Ajout styles dropdown et profil |
| `config/packages/security.yaml` | `role_hierarchy` + `access_control` |

## Résumé

- Création de la route `/profil` (`app_profile`), sécurisée par `ROLE_USER`
- La page affiche : avatar, identité, biographie, formations responsables, liens externes
- Le nom d'utilisateur dans la navbar devient un dropdown Bootstrap avec :
  - "Mon profil" (toujours)
  - "Administration" (uniquement si `ROLE_ADMIN`)
  - "Déconnexion"
- `role_hierarchy` ajouté : `ROLE_SUPER_ADMIN → ROLE_ADMIN → ROLE_USER`
- `access_control` activé pour `/profil` (ROLE_USER) et `/admin` (ROLE_ADMIN)

## Raison

Demande utilisateur : page profil accessible via dropdown sur le nom, avec lien admin conditionnel.
