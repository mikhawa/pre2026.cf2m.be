# 061 — Historique des révisions par type de contenu

**Modèle** : Sonnet
**Justification** : Controllers métier + service + menu EasyAdmin

## Objectif
Afficher un historique filtré par type de contenu (Formations / Works / Pages) dans EasyAdmin,
avec possibilité de restaurer n'importe quelle version, et sauvegarde systématique pour tous
les profils (admin = APPROVED, formateur = PENDING).

## Fichiers créés
- `src/Controller/Admin/FormationRevisionCrudController.php` — historique filtré 'formation'
- `src/Controller/Admin/WorksRevisionCrudController.php` — historique filtré 'works'
- `src/Controller/Admin/PageRevisionCrudController.php` — historique filtré 'page'

## Fichiers modifiés
- `src/Controller/Admin/RevisionCrudController.php`
  - `revisionService` et `em` passés en `protected` (accessibles par les sous-classes)
  - `redirectToIndex` passé en `protected`
  - Nouvelle action `appliquerVersion` (#[AdminRoute]) pour appliquer n'importe quelle version
- `src/Service/RevisionService.php`
  - Nouvelle méthode `appliquerVersion(Revision $source, User $reviewer)` : sauvegarde l'état
    courant en tant que nouvelle révision APPROVED, puis applique les données de la source
- `src/Controller/Admin/DashboardController.php`
  - Menu restructuré avec `MenuItem::subMenu()` pour Formations, Works, Pages
  - Chaque sous-menu contient : Liste + Historique

## Comportement
| Profil | Modification | Entrée historique |
|--------|-------------|-------------------|
| Admin/Super-admin | Immédiate | Révision STATUS_APPROVED |
| Formateur | Soumise en attente | Révision STATUS_PENDING |

| Action | Description |
|--------|-------------|
| Approuver | PENDING → appliqué au live (existant) |
| Rejeter | PENDING → rejeté, live inchangé (existant) |
| Restaurer | Applique previousData de la révision (existant) |
| **Appliquer cette version** | Nouveau : applique revision.data au live (navigation historique libre) |
