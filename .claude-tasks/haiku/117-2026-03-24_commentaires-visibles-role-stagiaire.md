# 117 — Commentaires visibles par ROLE_STAGIAIRE (ses propres uniquement)

**Modèle** : Haiku
**Justification** : Ajout permission + filtre query, 2 fichiers

## Changement
Un ROLE_STAGIAIRE peut consulter ses propres commentaires dans l'admin.
Un ROLE_FORMATEUR ou supérieur voit toujours tous les commentaires.

## Fichiers modifiés
- `src/Controller/Admin/CommentCrudController.php`
  - Injection de `Security`
  - Override `createIndexQueryBuilder` : filtre `entity.user = currentUser` si pas ROLE_FORMATEUR
  - Permissions : INDEX/DETAIL → ROLE_STAGIAIRE, EDIT → ROLE_FORMATEUR
- `src/Controller/Admin/DashboardController.php`
  - Menu "Commentaires" : permission abaissée à ROLE_STAGIAIRE
