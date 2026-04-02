# Tâche 089 — Affichage des champs modifiés dans l'historique EasyAdmin

**Modèle** : Sonnet
**Justification** : Modification d'une méthode de service métier

## Fichier modifié
- `src/Service/RevisionService.php` — méthode `buildTypedHistoryDiffHtml()`

## Résumé

### Cas `$before === null` (création initiale)
Badge « Création initiale » + liste de tous les champs non vides affichés en vert. Champs riches (description, content) avec bouton collapse.

### Cas `$before !== null` (modification)
Résumé « N champ(s) modifié(s) » ajouté en tête. Seuls les champs dont la valeur diffère sont listés (ancienne valeur en rouge ~~barré~~, nouvelle en vert souligné).

## Résultat
✅ 89/89 tests | `cache:clear` OK
