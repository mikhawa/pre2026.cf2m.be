# 084 — Correction contexte EasyAdmin — page révisions en attente

**Date** : 2026-03-20 22:00
**Tâche** : 094

## Fichiers modifiés / créés
- `src/Controller/Admin/RevisionsPendantesController.php` *(nouveau)*
- `src/Controller/Admin/DashboardController.php`

## Résumé
La page `/admin/revisions-en-attente` lançait `Impossible to access an attribute ("i18n") on a null variable in @EasyAdmin/layout.html.twig` car le contexte EasyAdmin n'était pas initialisé.

### Cause technique
EasyAdmin initialise son contexte (`AdminContext`) uniquement sur la route principale `/admin` via `#[AdminDashboard]`. Un `#[Route]` standard sur `DashboardController` ne déclenche pas ce mécanisme, donc `@EasyAdmin/page/content.html.twig` reçoit un contexte null.

### Solution
- Création de `RevisionsPendantesController extends AbstractCrudController` avec `#[AdminRoute(path: '/revisions-en-attente')]` — ce mécanisme passe par le routing EasyAdmin et initialise correctement le contexte.
- `DashboardController::revisionsPendantesRedirect()` : route Symfony classique qui redirige vers l'URL EasyAdmin (pour que `path('admin_revisions_en_attente')` fonctionne dans les templates Twig).
- `AdminUrlGenerator` injecté dans le constructeur de `DashboardController` pour générer l'URL dans `configureMenuItems()`.

## Raison
Erreur 500 à l'accès de la page `/admin/revisions-en-attente`.
