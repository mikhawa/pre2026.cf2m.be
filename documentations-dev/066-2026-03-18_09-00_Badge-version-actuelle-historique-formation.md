# 066 — Badge "Version actuelle" dans l'historique Formation

**Date** : 2026-03-18 09:00
**Branch** : feature/revisions-to-history

## Fichiers modifiés
- `src/Service/RevisionService.php`
- `src/Controller/Admin/FormationCrudController.php`
- `templates/admin/formation/historique.html.twig`

## Résumé des changements
Dans la page d'historique d'une formation (`/admin/formation/{id}/historique`), la révision dont les données correspondent exactement au contenu live de la formation est maintenant identifiée.

### Mécanisme
1. `RevisionService::getLiveFormationSnapshot(Formation)` — nouvelle méthode publique exposant le snapshot live de la formation
2. `FormationCrudController::historiqueFormation()` — calcule le snapshot live puis compare (`===`) chaque `revision->getData()` pour flag `isCurrent`
3. Template Twig — affiche un badge bleu "Version actuelle" pour l'entrée correspondante ; le bouton "Restaurer cette version" est masqué (aucune action si déjà live)

## Raison
UX : l'admin ne pouvait pas distinguer quelle version était actuellement publiée dans la timeline.
