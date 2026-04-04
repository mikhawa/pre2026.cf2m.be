---
date: 2026-04-04 10:00
---

# 098 — Correction popup préinscription : responsive mobile

## Fichier modifié

- `templates/formation/show.html.twig`

## Résumé

Sur smartphone, la modal de préinscription dépassait la hauteur de l'écran. Les boutons « Annuler » et « Envoyer ma demande » n'étaient pas accessibles.

## Correction

Ajout de la classe Bootstrap `modal-fullscreen-sm-down` sur le `.modal-dialog` :

```diff
- <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
+ <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
```

En dessous de 576px (breakpoint `sm`), la modal passe en plein écran. Le header et le footer sont fixés, le body est scrollable — les boutons restent toujours visibles.

## Raison

Signalement utilisateur : impossible de cliquer sur « Annuler » ou « Envoyer ma demande » sur mobile.
