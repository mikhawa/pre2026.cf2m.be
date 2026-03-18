# 068 — 2026-03-18 11:00 — Historique Works et Pages identique à Formation

## Fichiers modifiés
- `src/Repository/RevisionRepository.php`
- `src/Service/RevisionService.php`
- `src/Controller/Admin/WorksCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `templates/admin/works/historique.html.twig` (nouveau)
- `templates/admin/page/historique.html.twig` (nouveau)

## Résumé
Refactoring du repository : `findByEntityId(type, id)` générique (Formation délègue).
Ajout de wrappers publics `getLivePageSnapshot()` et `getLiveWorksSnapshot()` dans RevisionService.
WorksCrudController et PageCrudController reçoivent le même traitement que FormationCrudController :
- Bouton Historique (N) en jaune dans l'index, l'édition et le détail (ROLE_ADMIN)
- Page timeline avec badge "Version actuelle", diff git-like, boutons d'action
- Ordre des boutons : Sauvegarder | Continuer | Historique

## Raison
La fonctionnalité d'historique existait uniquement pour les Formations.
Works et Pages doivent bénéficier du même niveau de traçabilité.
