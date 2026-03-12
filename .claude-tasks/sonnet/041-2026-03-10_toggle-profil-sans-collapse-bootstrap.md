# 041 — Toggle profil sans collapse Bootstrap

**Date** : 2026-03-10
**Modèle** : Sonnet
**Justification** : Correction JS/template frontend

## Problème
Le composant Bootstrap collapse introduisait un délai d'animation, rendant les deux blocs (profil et formulaire) momentanément visibles en même temps. L'utilisateur exigeait un remplacement immédiat sans transition.

## Fichiers modifiés
- `templates/profil/index.html.twig`
- `assets/styles/app.css`

## Résumé des changements

### templates/profil/index.html.twig
- Supprimé `data-bs-toggle`, `data-bs-target`, `aria-expanded`, `aria-controls` du bouton
- Remplacé les deux `<span>` label par un texte simple
- Ajouté `id="cf2m-profile-toggle"` sur le bouton
- Remplacé `class="collapse"` par `class="d-none"` sur le wrapper du formulaire
- Réécrit le JS : toggle direct via `classList.add/remove('d-none')` sur les deux blocs
- Texte du bouton mis à jour dynamiquement ("Modifier mon profil" ↔ "Mon profil")
- localStorage conservé pour la persistance d'état

### assets/styles/app.css
- Supprimé les règles `.cf2m-toggle-edit[aria-expanded=...]` devenues obsolètes

## Résultat
Clic sur "Modifier mon profil" → remplacement immédiat du bloc profil par le formulaire, sans animation ni superposition.
