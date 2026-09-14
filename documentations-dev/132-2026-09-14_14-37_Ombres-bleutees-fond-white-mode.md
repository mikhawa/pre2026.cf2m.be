# 132 — Ombres bleutées légères sur le fond white mode

**Date** : 2026-09-14 14:37
**Fichier modifié** : `assets/styles/app.css`

## Résumé

Le fond `body` des pages intérieures en white mode (`#f5f7f9`, introduit en tâche 130)
reçoit désormais 4 dégradés radiaux bleu/cyan très atténués, positionnés exactement comme
les 4 dégradés du fond `body` en dark mode (halo cyan haut-droite, glow bleu gauche, glow
bleu bas-droite, vignette basse) — mais avec des opacités réduites à 0.05–0.07 (contre
0.18–0.40 en dark) pour rester très discrets sur fond clair.

## Raison

Demande explicite de l'utilisateur : « le fond blanc des autres pages doit être un blanc
avec de légères ombres bleutées (comme dans le dark, mais bcp plus clair) ». Reprend donc
la structure existante du dark mode plutôt que d'inventer un nouveau traitement.
