# 133 — Intensification des ombres bleutées du fond white mode

**Date** : 2026-09-14 14:41
**Fichier modifié** : `assets/styles/app.css`

## Résumé

Les opacités des 4 dégradés radiaux ajoutés en tâche 132 sont remontées (halos : 0.05-0.07
→ 0.15-0.20 ; vignette basse : 0.45 → 0.60) pour que les ombres bleutées soient réellement
visibles sur le fond blanc `#f5f7f9`, plutôt que quasi imperceptibles.

## Raison

L'utilisateur a signalé ne pas voir l'effet. Diagnostic : les mêmes opacités que le dark
mode ne produisent pas le même impact visuel sur fond clair — un rgba à faible alpha se
dilue beaucoup plus sur blanc que sur un fond sombre, où il ressort par contraste. Valeurs
augmentées en conséquence, en gardant la même structure de dégradés (positions identiques
au dark mode).
