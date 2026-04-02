# 124 — Correction lisibilité alertes Bootstrap en dark mode

**Modèle** : Haiku  
**Justification** : Correction CSS simple, pas de logique métier

## Problème
Le message flash `'Votre profil a été mis à jour.'` (et autres alertes Bootstrap) était illisible en dark mode car Bootstrap surchargeait les variables CSS avec des couleurs dark-on-light (texte vert foncé sur fond vert clair).

## Fichiers modifiés
- `assets/styles/app.css`

## Résumé des changements
- Ajout de surcharges dark mode (défaut) pour `.alert-success`, `.alert-warning`, `.alert-danger`, `.alert-info` : texte clair sur fond semi-transparent coloré
- Refactoring des surcharges light mode pour restaurer explicitement les 3 variables Bootstrap (`--bs-alert-color`, `--bs-alert-bg`, `--bs-alert-border-color`) au lieu de seulement la couleur du texte
