# 067 — 2026-03-18 10:00 — Style bouton "Sauvegarder et continuer" via data-attribute

## Fichiers modifiés
- `assets/styles/admin.css`
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`

## Résumé
Remplacement de l'approche `setCssClass('btn btn-ea-continue')` + surcharge `--button-bg`
par `asWarningAction()` + `setHtmlAttributes(['data-ea-btn' => 'continue'])`.

Le fond du bouton est désormais géré par le variant `warning` natif d'EasyAdmin,
qui s'adapte automatiquement aux modes clair et sombre via ses propres variables CSS.
Le CSS personnalisé ne surcharge que :
- `--button-color: #0d6efd` (texte bleu hors survol)
- `--button-hover-bg: #0d6efd` (fond bleu au survol)
- `--button-hover-color: #fff` (texte blanc au survol)
- `--button-hover-border-color: #0a58ca`

## Raison
La surcharge de `--button-bg` était ignorée en mode sombre car EasyAdmin définit
ses variables dans `.ea-dark-scheme {}` avec une spécificité plus forte que le sélecteur global.
Déléguer le fond au variant natif résout définitivement le problème.
