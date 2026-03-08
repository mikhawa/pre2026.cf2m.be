# 033 — EasyAdmin pour ROLE_SUPER_ADMIN

**Date** : 2026-03-07 11h00

## Fichiers créés

| Fichier | Entité |
|---|---|
| `src/Controller/Admin/DashboardController.php` | Dashboard |
| `src/Controller/Admin/UserCrudController.php` | User |
| `src/Controller/Admin/FormationCrudController.php` | Formation |
| `src/Controller/Admin/ContactMessageCrudController.php` | ContactMessage |
| `src/Controller/Admin/WorksCrudController.php` | Works |
| `src/Controller/Admin/PageCrudController.php` | Page |
| `src/Controller/Admin/InscriptionCrudController.php` | Inscription |
| `src/Controller/Admin/CommentCrudController.php` | Comment |
| `src/Controller/Admin/RatingCrudController.php` | Rating |
| `src/Controller/Admin/PartenaireCrudController.php` | Partenaire |
| `templates/admin/dashboard.html.twig` | — |

## Modifications

- `config/packages/security.yaml` : `^/admin` → `ROLE_SUPER_ADMIN`

## Sécurité

- Double protection : `access_control` (ROLE_SUPER_ADMIN) + `#[IsGranted('ROLE_SUPER_ADMIN')]` sur le DashboardController
- `role_hierarchy` existant : `ROLE_SUPER_ADMIN → ROLE_ADMIN → ROLE_USER`

## Fonctionnalités par CRUD

- **User** : email, userName, rôles (badges), statut, biographie, liens — filtre par rôle
- **Formation** : titre, slug, statut (badge), dates, responsables, description (TextEditor) — filtre statut
- **ContactMessage** : lecture seule (NEW désactivé), toggle "lu", readBy
- **Works** : titre, slug, statut, formation, étudiants, description (TextEditor)
- **Page** : titre, slug, statut, contenu (TextEditor), auteurs
- **Inscription** : lecture seule (NEW désactivé), toggle "traitée", treatAt, treatBy
- **Comment** : contenu, approbation (toggle), filtres user/works
- **Rating** : note 1-5, user, works/comments liés
- **Partenaire** : nom, actif (toggle), url, description
