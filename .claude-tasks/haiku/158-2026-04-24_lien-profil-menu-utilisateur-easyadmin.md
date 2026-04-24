# 158 — Lien profil dans le menu utilisateur EasyAdmin

- **Modèle** : Haiku
- **Justification** : Ajout simple d'un item de menu dans `configureUserMenu()`
- **Date** : 2026-04-24

## Fichiers modifiés
- `src/Controller/Admin/DashboardController.php`

## Résumé
Ajout de la méthode `configureUserMenu()` dans `DashboardController` pour afficher deux liens dans le menu déroulant en haut à droite de l'administration EasyAdmin, avant le lien de déconnexion :
1. "Site public" (icône `fa-globe`, route `app_home`)
2. "Mon profil" (icône `fa-user`, route `app_profile`)

## Résultat
Les deux liens sont visibles dans le user-menu EasyAdmin pour tous les utilisateurs connectés, dans cet ordre, au-dessus de la déconnexion.
