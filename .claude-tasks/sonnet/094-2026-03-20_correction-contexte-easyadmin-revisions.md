# Tâche 094 — Correction contexte EasyAdmin page révisions en attente

**Modèle** : Sonnet
**Justification** : Bug architectural — contexte EasyAdmin non initialisé

## Fichiers modifiés / créés
- `src/Controller/Admin/RevisionsPendantesController.php` *(nouveau)*
- `src/Controller/Admin/DashboardController.php`

## Résumé
Erreur : `Impossible to access an attribute ("i18n") on a null variable in @EasyAdmin/layout.html.twig`

### Cause
`#[Route]` sur `DashboardController` crée une route Symfony classique. Le listener EasyAdmin (`AdminContextProvider`) n'initialise le contexte QUE pour la route `/admin` (via `#[AdminDashboard]`). Les templates `@EasyAdmin/page/content.html.twig` nécessitent ce contexte.

### Correction
1. Création de `RevisionsPendantesController extends AbstractCrudController` avec `#[AdminRoute]` — seul mécanisme qui initialise correctement le contexte EasyAdmin pour les pages personnalisées.
2. `DashboardController::revisionsPendantes()` transformé en simple redirection vers l'URL EasyAdmin du nouveau controller.
3. `DashboardController` injecte maintenant `AdminUrlGenerator` dans le constructeur pour générer l'URL EasyAdmin dans `configureMenuItems()` (via `MenuItem::linkToUrl()`).
4. `path('admin_revisions_en_attente')` continue de fonctionner dans les templates Twig (ex. profil) via la redirection.

## Résultat
✅ Page accessible sans erreur de contexte EasyAdmin.
