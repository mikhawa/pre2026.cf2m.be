# 158 — Lien profil dans le menu utilisateur EasyAdmin

- **Modèle** : Haiku
- **Justification** : Ajout simple d'un item de menu dans `configureUserMenu()`
- **Date** : 2026-04-24

## Fichiers modifiés
- `src/Controller/Admin/DashboardController.php`

## Résumé
Ajout de la méthode `configureUserMenu()` dans `DashboardController` pour afficher un lien "Mon profil" (route `app_profile`) dans le menu déroulant en haut à droite de l'administration EasyAdmin, juste avant le lien de déconnexion.

## Résultat
Lien "Mon profil" visible dans le user-menu EasyAdmin pour tous les utilisateurs connectés.
