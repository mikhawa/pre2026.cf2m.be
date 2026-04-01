# 125 — Crop manuel avatar 80×80 via Stimulus + Cropper.js v2

**Modèle** : Sonnet  
**Justification** : Intégration d'une librairie JS externe avec logique Stimulus non triviale

## Fichiers modifiés
- `assets/controllers/avatar_crop_controller.js` (créé)
- `templates/profil/edit.html.twig`
- `assets/styles/app.css`

## Résumé
- Nouveau Stimulus controller `avatar-crop` : intercepte le changement de fichier, ouvre un modal Bootstrap avec Cropper.js v2 (web components), exporte la sélection en 80×80 WebP via Canvas API, réinjecte dans le file input via DataTransfer, annulation réinitialise le file input
- Template `edit.html.twig` : ajout `data-controller`, targets Stimulus sur le file input et les éléments de prévisualisation, modal avec les éléments `<cropper-*>`, suppression de l'ancien `<script>` inline
- CSS : `.cf2m-crop-container` (360px, fond noir), `.cf2m-crop-modal-content` (dark/light mode)
- L'image est recadrée côté client → aucun changement backend nécessaire
