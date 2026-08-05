# 125 — Restyle accueil : hero, cartes formations, partenaires

**Date** : 2026-08-05 19:06
**Fichier(s) modifié(s)** : `assets/styles/app.css`

## Résumé
Étape 3 du restyle visuel "raffiné white/dark" (voir tâche `.claude-tasks/sonnet/193-...md`), en deux passes :

1. Rayons de bordure passés en "pill" (999px) sur le bouton CTA du hero, les CTA des cartes formations, le badge de statut et les chips partenaires ; rayon des cartes agrandi (0.75rem → 1.25rem) ; séparateur de section ajouté en dark mode sur `.cf2m-partners`.
2. Suite au retour "aucune ressemblance au thème envoyé" : comparaison objective par rendu statique du mockup, puis refonte plus profonde du hero (suppression du panneau vitré — le texte repose directement sur la photo `hero-bg.jpg`, ré-exploitée) et des cartes (CTA "En savoir plus" en lien texte fléché, ombre de repos allégée), plus de respiration verticale sur les sections formations/partenaires. La couleur par formation (`colorPrimary`/`colorSecondary`) est conservée à la demande de l'utilisateur.

Correction annexe : bug de contraste sur le bouton CTA du hero en light mode (texte invisible, conflit de spécificité CSS entre `[data-theme="light"] a` et `.cf2m-hero-btn`).

## Raison
Rapprocher la page d'accueil de la charte du mockup de référence (`datas/Site raffiné whitedark mode/`) sans modifier le markup Twig ni le modèle de données — décision utilisateur actée dans les tâches 192/193.
