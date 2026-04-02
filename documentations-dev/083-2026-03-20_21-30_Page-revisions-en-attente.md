# 083 — Page dédiée "Révisions en attente"

**Date** : 2026-03-20 21:30
**Tâche** : 093

## Fichiers modifiés / créés
- `src/Repository/FormationHistoryRepository.php`
- `src/Repository/PageHistoryRepository.php`
- `src/Repository/WorksHistoryRepository.php`
- `src/Controller/Admin/DashboardController.php`
- `templates/admin/revisions-en-attente.html.twig` *(nouveau)*
- `templates/profil/index.html.twig`

## Résumé
Création d'une page centralisée `/admin/revisions-en-attente` permettant à un admin de voir et traiter toutes les révisions en attente sans naviguer dans chaque entité séparément.

### Repositories
Ajout de `findAllPending(): array` et `findByVersion(Entite, int): ?History` dans les 3 repositories d'historique.

### DashboardController
Nouvelle action `revisionsPendantes()` :
- Agrège les révisions PENDING des 3 types
- Calcule le diff (snapshot après vs snapshot version précédente)
- Construit les URLs approve/reject/historique via `AdminUrlGenerator`
- Passe les données au template

Nouveau menu item "Révisions en attente" dans la section Interactions, avec badge danger sur le total.

### Template
Cards Bootstrap pour chaque révision en attente :
- Badge type (Formation/Page/Works), titre, version, auteur, date
- Diff HTML inline
- Boutons "Approuver & appliquer", "Rejeter", "Historique complet"

### Profil
Lien de la bannière d'alerte mis à jour : `path('admin_revisions_en_attente')`.

## Raison
L'admin devait naviguer manuellement dans chaque liste de contenu pour trouver les révisions en attente.
