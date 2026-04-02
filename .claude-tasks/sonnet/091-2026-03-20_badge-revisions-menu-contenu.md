# Tâche 091 — Correction badge révisions en attente dans le menu

**Modèle** : Sonnet
**Justification** : Correction d'un bug de navigation dans le DashboardController

## Fichier modifié
- `src/Controller/Admin/DashboardController.php`

## Résumé
Le menu "Révisions en attente" pointait vers `FormationCrudController` (placeholder laissé lors de la suppression de `RevisionCrudController` en Phase 5).

### Correction
- Suppression de l'entrée orpheline "Révisions en attente" dans la section Interactions
- Ajout de badges individuels (danger) sur chaque item de contenu :
  - **Formations** — `formationHistoryRepo->countPending()`
  - **Works** — `worksHistoryRepo->countPending()`
  - **Pages** — `pageHistoryRepo->countPending()`
- Les 3 repos déjà injectés dans le constructeur sont maintenant utilisés dans la section Contenu

## Résultat
✅ Badge rouge sur chaque type de contenu ayant des révisions en attente — navigation directe vers la liste concernée.
