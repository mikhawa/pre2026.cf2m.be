# 034 — Redirection vers le profil après connexion

**Date** : 2026-03-08 09:00
**Branche** : Navigation

## Fichiers modifiés
- `config/packages/security.yaml`
- `src/Controller/SecurityController.php`

## Résumé des changements

### `security.yaml`
- `default_target_path` changé de `app_home` → `app_profile`
- `always_use_default_target_path: false` conservé (respecte le referer si présent)

### `SecurityController.php`
- Redirection si déjà connecté : `app_home` → `app_profile`

## Raison
L'utilisateur doit atterrir sur sa page de profil après s'être connecté, plutôt que sur la page d'accueil publique.
