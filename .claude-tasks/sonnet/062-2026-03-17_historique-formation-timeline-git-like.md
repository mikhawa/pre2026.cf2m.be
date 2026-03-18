# 062 — Historique Formation : timeline git-like dans EasyAdmin

**Modèle** : Sonnet
**Justification** : Controller, service, repository, template — fonctionnalité métier complète

## Objectif
Afficher un historique complet des modifications d'une Formation directement depuis son
édition EasyAdmin, sous forme de timeline git-like avec diff des changements.

## Fichiers modifiés
- `src/Repository/RevisionRepository.php` — ajout `findByFormationId(int): array`
- `src/Service/RevisionService.php` — ajout `buildHistoryDiffHtml(Revision): string`
  (compare previousData↔data, affiche **seulement** les champs modifiés)
- `src/Controller/Admin/RevisionCrudController.php` — `redirectToIndex` supporte `?returnUrl=`
- `src/Controller/Admin/FormationCrudController.php`
  - Imports: RevisionRepository, AdminUrlGenerator, AdminRoute, AdminContext, Response
  - Bouton "Historique" (ROLE_ADMIN) sur INDEX, EDIT, DETAIL
  - Action `historiqueFormation` : charge les révisions, construit les URLs d'action avec returnUrl

## Fichiers créés
- `templates/admin/formation/historique.html.twig` — timeline Bootstrap avec :
  - Nœuds colorés sur ligne verticale (vert/orange/rouge selon statut)
  - En-tête : ID #N, date, auteur, badge statut
  - Corps : diff git-like (seulement champs modifiés, del→ins)
  - Champs riches (description) : collapsible avec extrait 120 chars
  - Boutons : Approuver/Rejeter (PENDING) | Restaurer cette version (APPROVED)
  - Retour à la formation courante via `?returnUrl=` après action

## Comportement
| Profil | Action de sauvegarde | Apparaît dans l'historique |
|--------|---------------------|--------------------------|
| Admin  | Immédiate (APPROVED) | ✅ Vert, bouton Restaurer |
| Formateur | En attente (PENDING) | ⏳ Orange, boutons Approuver/Rejeter |
| Rejeté | — | 🔴 Rouge, aucune action |
