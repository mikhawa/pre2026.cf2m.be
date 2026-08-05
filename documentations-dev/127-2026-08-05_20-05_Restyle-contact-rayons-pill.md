# 127 — Restyle contact : rayons pill sur champs et bouton d'envoi

**Date** : 2026-08-05 20:05
**Fichier(s) modifié(s)** : `assets/styles/app.css`

## Résumé
Étape 5 du restyle visuel "raffiné white/dark" (voir tâche `.claude-tasks/sonnet/195-...md`) : la page `/contact` (formulaire Symfony réel + Turnstile) héritait déjà des rayons de carte de l'étape 3, mais restait le dernier endroit du site avec des champs (`.cf2m-input`, `0.5rem → 0.75rem`) et un bouton plein (`.cf2m-btn-submit`, `0.5rem → 999px` pill) non harmonisés avec la charte "pill" désormais appliquée partout ailleurs (hero, cartes formations, partenaires). Le changement sur `.cf2m-btn-submit` profite aussi à la page `contact/success.html.twig` (classe partagée).

## Raison
Cohérence visuelle de fin de parcours — aucune fonctionnalité modifiée, uniquement des rayons de bordure.
