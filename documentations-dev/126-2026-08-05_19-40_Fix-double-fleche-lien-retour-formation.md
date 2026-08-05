# 126 — Fix : double flèche sur le lien "Retour aux formations"

**Date** : 2026-08-05 19:40
**Fichier(s) modifié(s)** : `assets/styles/app.css`

## Résumé
Étape 4 du restyle visuel "raffiné white/dark" (voir tâche `.claude-tasks/sonnet/194-...md`) : vérification de la fiche formation (`/formation/{slug}`) après les changements de l'étape 3. La page héritait déjà du style rafraîchi des cartes, mais le lien "← Retour aux formations" affichait une flèche en double ("← RETOUR AUX FORMATIONS →") car il partage la classe `.btn-outline-secondary` avec le CTA "En savoir plus" de l'accueil, sur lequel une flèche `::after` avait été ajoutée. Règle scopée à `.page-home` pour ne plus toucher la fiche formation.

## Raison
Régression détectée par capture d'écran (Playwright) lors de la vérification visuelle systématique — pas de nouvelle demande fonctionnelle, correctif de cohérence.
