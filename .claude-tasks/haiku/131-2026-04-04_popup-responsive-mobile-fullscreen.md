---
modèle: Haiku
justification: Correction CSS/classe Bootstrap — modification d'une seule ligne de template
date: 2026-04-04
---

# Tâche 131 — Correction popup préinscription trop grand sur mobile

## Fichiers modifiés

- `templates/formation/show.html.twig` — ajout de `modal-fullscreen-sm-down` sur `.modal-dialog`

## Résumé

Sur smartphone, la modal de préinscription dépassait la hauteur du viewport, rendant les boutons « Annuler » et « Envoyer ma demande » inaccessibles.

Correction : ajout de la classe Bootstrap `modal-fullscreen-sm-down` qui passe la modal en plein écran sur les écrans < 576px. Le header et le footer (avec les boutons) restent fixés en haut/bas, le body devient scrollable.

## Résultat

Les boutons sont désormais toujours visibles sur mobile, et le contenu du formulaire défile correctement à l'intérieur de la modal.
