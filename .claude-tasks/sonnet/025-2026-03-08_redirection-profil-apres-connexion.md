# 025 - Redirection vers le profil après connexion

**Modèle** : Sonnet
**Justification** : Modification de configuration sécurité + controller
**Date** : 2026-03-08

## Fichiers modifiés
- `config/packages/security.yaml`
- `src/Controller/SecurityController.php`

## Résumé
- `default_target_path` changé de `app_home` vers `app_profile`
- Redirection si déjà connecté changée de `app_home` vers `app_profile`

## Résultat
Après connexion réussie, l'utilisateur est redirigé vers `/profil` au lieu de l'accueil.
