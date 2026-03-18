# 069 — Couleurs boutons EasyAdmin : Historique jaune, Save bleu/vert

**Modèle** : Sonnet
**Justification** : Configuration EasyAdmin + injection CSS

## Problème
EasyAdmin utilise son propre système de CSS variables pour les classes `btn-*`, qui ne correspondent pas aux couleurs Bootstrap classiques. `setCssClass('btn btn-primary')` donnait un bleu-violet EasyAdmin, pas un bleu pur ; `btn-warning` donnait un fond blanc avec texte ambre.

## Solution
Classes CSS personnalisées préfixées `btn-cf2m-*` + injection via `addHtmlContentToHead()`.

## Fichiers modifiés
- `src/Controller/Admin/FormationCrudController.php` — classes changées :
  - Historique : `btn btn-sm btn-cf2m-historique`
  - SAVE_AND_CONTINUE : `btn btn-cf2m-continue`
  - SAVE_AND_RETURN : `btn btn-cf2m-save`
- `src/Controller/Admin/DashboardController.php` — `configureAssets()` : ajout `addHtmlContentToHead()` avec les 3 classes CSS Bootstrap exactes

## Couleurs
- `btn-cf2m-historique` : `#ffc107` (jaune Bootstrap warning) + texte `#212529`
- `btn-cf2m-continue` : `#0d6efd` (bleu Bootstrap primary)
- `btn-cf2m-save` : `#198754` (vert Bootstrap success)
