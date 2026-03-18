# 070 — Style "Sauvegarder et continuer" : texte bleu, fond dark/light natif

**Modèle** : Sonnet
**Justification** : CSS personnalisé EasyAdmin + contrôleurs

## Fichiers modifiés
- `assets/styles/admin.css` — sélecteur `[data-ea-btn="continue"]` : uniquement `--button-color`, `--button-hover-*`
- `src/Controller/Admin/FormationCrudController.php` — `asWarningAction()` + `setHtmlAttributes(['data-ea-btn' => 'continue'])`
- `src/Controller/Admin/PageCrudController.php` — idem
- `src/Controller/Admin/WorksCrudController.php` — idem

## Résumé
Approche finale : `asWarningAction()` pour hériter du fond natif EasyAdmin (dark/light auto),
`setHtmlAttributes(['data-ea-btn' => 'continue'])` comme crochet CSS.
Le CSS ne touche que la couleur du texte (#0d6efd) et le hover (fond bleu, texte blanc).
Le fond au repos est identique aux boutons Historique et Sauvegarder les modifications.
