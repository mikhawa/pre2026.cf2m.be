# 071 — Historique Works et Pages : même système que Formation

**Modèle** : Sonnet
**Justification** : Controllers métier, services, templates

## Fichiers modifiés
- `src/Repository/RevisionRepository.php` — ajout de `findByEntityId(string $type, int $id)` générique ; `findByFormationId` délègue
- `src/Service/RevisionService.php` — ajout de `getLivePageSnapshot()` et `getLiveWorksSnapshot()`
- `src/Controller/Admin/WorksCrudController.php` — inject `RevisionRepository`, action `historiqueWorks` (warning + count), route `historiqueWorks`, reorder boutons
- `src/Controller/Admin/PageCrudController.php` — idem pour `historiquePage`
- `templates/admin/works/historique.html.twig` — nouveau template timeline
- `templates/admin/page/historique.html.twig` — nouveau template timeline

## Résumé
Les pages Works et Pages disposent désormais du même bouton Historique (N) et de la même
page timeline git-like que Formations : badge "Version actuelle", diff par champ,
boutons Approuver/Rejeter/Restaurer, returnUrl vers la page d'historique.
