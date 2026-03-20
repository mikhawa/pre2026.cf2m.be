# 081 — Correction badge révisions en attente dans le menu dashboard

**Date** : 2026-03-20 21:00
**Tâche** : 091

## Fichier modifié
- `src/Controller/Admin/DashboardController.php`

## Résumé
Après la Phase 5 (suppression de `RevisionCrudController`), l'entrée de menu "Révisions en attente" pointait incorrectement vers `FormationCrudController`, causant une redirection vers la liste des formations.

### Correctif
- Suppression de l'entrée orpheline "Révisions en attente" (section Interactions)
- Remplacement par des badges individuels sur chaque item de la section Contenu :
  - **Formations** : badge danger si `formationHistoryRepo->countPending() > 0`
  - **Works** : badge danger si `worksHistoryRepo->countPending() > 0`
  - **Pages** : badge danger si `pageHistoryRepo->countPending() > 0`

## Raison
Le menu "Révision en attente" redirige vers admin/formation. Bug introduit lors de la Phase 5 de la refactorisation des tables d'historique typées (suppression de `RevisionCrudController`).
