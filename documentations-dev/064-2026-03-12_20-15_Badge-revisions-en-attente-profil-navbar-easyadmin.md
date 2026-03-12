# 064 — Badge révisions en attente sur le profil, la navbar et EasyAdmin

**Date** : 2026-03-12 20:15
**Modèle** : Sonnet

## Fichiers modifiés
- `src/Twig/NavigationExtension.php`
- `src/Controller/Admin/DashboardController.php`
- `templates/base.html.twig`
- `templates/profil/index.html.twig`

## Fonctionnalités ajoutées

### Fonction Twig `pending_revisions_count()`
Ajoutée dans `NavigationExtension` via `RevisionRepository::findPendingCount()`. Mise en cache mémoire pour éviter plusieurs requêtes par page.

### Navbar frontend (base.html.twig)
Badge rouge affiché à côté de "Administration" dans le dropdown utilisateur, uniquement si `is_granted('ROLE_ADMIN')` et count > 0.

### Page profil (profil/index.html.twig)
Alerte orange cliquable (lien vers `/admin/revision`) affichée en haut de la page pour les admins, avec le nombre de révisions en attente. Dismissable via Bootstrap.

### Sidebar EasyAdmin (DashboardController)
Injection de `RevisionRepository`, badge `danger` sur le menu "Révisions" via `->setBadge($count, 'danger')`. Affiché uniquement si count > 0 (null sinon pour ne pas afficher "0").
