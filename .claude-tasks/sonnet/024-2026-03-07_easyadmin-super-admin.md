# 024 — EasyAdmin — accès ROLE_SUPER_ADMIN, toutes permissions

**Date** : 2026-03-07
**Modèle** : Sonnet (controllers admin, configuration)

## Fichiers créés / modifiés

- `src/Controller/Admin/DashboardController.php` — créé : `#[AdminDashboard]` `/admin`, `#[IsGranted('ROLE_SUPER_ADMIN')]`, menu complet
- `src/Controller/Admin/UserCrudController.php` — créé
- `src/Controller/Admin/FormationCrudController.php` — créé
- `src/Controller/Admin/ContactMessageCrudController.php` — créé (NEW désactivé)
- `src/Controller/Admin/WorksCrudController.php` — créé
- `src/Controller/Admin/PageCrudController.php` — créé
- `src/Controller/Admin/InscriptionCrudController.php` — créé (NEW désactivé)
- `src/Controller/Admin/CommentCrudController.php` — créé
- `src/Controller/Admin/RatingCrudController.php` — créé
- `src/Controller/Admin/PartenaireCrudController.php` — créé
- `templates/admin/dashboard.html.twig` — créé
- `config/packages/security.yaml` — `/admin` restreint à `ROLE_SUPER_ADMIN`

## Résumé

Menu organisé en 4 sections : Contenu / Utilisateurs / Interactions / Communication.
Chaque CRUD configure : labels FR, tri par défaut, recherche, pagination, filtres métier.
ContactMessage et Inscription ont le bouton "Nouveau" désactivé (données issues du frontend).
