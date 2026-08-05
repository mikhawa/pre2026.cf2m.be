# 125 — Restyle accueil : hero, cartes formations, partenaires

**Date** : 2026-08-05 19:06
**Fichier(s) modifié(s)** : `assets/styles/app.css`

## Résumé
Étape 3 du restyle visuel "raffiné white/dark" (voir tâche `.claude-tasks/sonnet/193-...md`) : rayons de bordure passés en "pill" (999px) sur le bouton CTA du hero, les CTA des cartes formations, le badge de statut et les chips partenaires ; rayon des cartes agrandi (0.75rem → 1.25rem) ; ajout d'un séparateur de section en dark mode sur `.cf2m-partners` (déjà présent en light).

Correction annexe : bug de contraste sur le bouton CTA du hero en light mode (texte invisible, conflit de spécificité CSS entre `[data-theme="light"] a` et `.cf2m-hero-btn`).

## Raison
Rapprocher la page d'accueil de la charte du mockup de référence (`datas/Site raffiné whitedark mode/`) sans modifier le markup Twig ni le modèle de données — décision utilisateur actée dans les tâches 192/193.
