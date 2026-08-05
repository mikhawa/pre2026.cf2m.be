# 128 — Restyle login : rayons pill sur champs et bouton (composant partagé)

**Date** : 2026-08-05 20:25
**Fichier(s) modifié(s)** : `assets/styles/app.css`

## Résumé
Étape 6 du restyle visuel "raffiné white/dark" (voir tâche `.claude-tasks/sonnet/196-...md`) : harmonisation des rayons `.cf2m-login-input` (`0.6rem → 0.75rem`), `.cf2m-login-error` (`0.6rem → 0.75rem`) et `.cf2m-login-btn` (`0.6rem → 999px` pill), pour rester cohérent avec la charte posée aux étapes précédentes. Le composant `.cf2m-login-*` étant partagé par `login`, `forgot_password`, `registration/register`, `reset_password` et `two_factor`, un seul changement CSS couvre tout le parcours d'authentification.

## Raison
Cohérence visuelle de fin de parcours — aucune fonctionnalité modifiée, uniquement des rayons de bordure.
