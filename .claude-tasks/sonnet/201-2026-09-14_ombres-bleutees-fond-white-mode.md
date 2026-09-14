# 201 — Ombres bleutées légères sur le fond white mode des pages intérieures

**Modèle utilisé** : Sonnet

**Justification** : ajustement CSS ciblé (dégradés), continuité directe de la tâche 199.

## Contexte

Suite à la tâche 199 (fond `body` en light mode passé à `#f5f7f9` uni), l'utilisateur a
précisé vouloir un fond blanc avec de légères ombres bleutées, **sur le même principe que
le dark mode** (qui n'est pas un fond uni : `body` en dark applique 4 dégradés radiaux
bleus/cyan superposés sur `#050e18`, cf. `assets/styles/app.css` lignes 76-89), mais en
version beaucoup plus claire.

## Fichiers modifiés

- `assets/styles/app.css` — `[data-theme="light"] body` : ajout de 4 `radial-gradient`
  reprenant exactement les mêmes positions/formes que le dark mode, avec des couleurs et
  opacités très réduites (0.05 à 0.07 pour les 3 halos cyan/bleu, 0.45 pour la vignette
  basse qui passe d'un noir quasi opaque en dark à un bleu-gris très clair en light).

## Résumé

Le fond des pages intérieures en white mode n'est plus un aplat `#f5f7f9` uniforme : il
reprend la structure de dégradés bleutés du dark mode (halo cyan en haut à droite, glow
bleu à gauche, glow bleu en bas à droite, vignette basse), rendue quasi imperceptible
mais présente, pour un rendu cohérent entre les deux thèmes.

## Résultat

Vérifié par lecture du CSS et par le CSS effectivement servi par nginx/AssetMapper après
cherry-pick sur `fix/10-colors-white-and-black-mode` (digest changé, gradients présents
dans le fichier compilé). Pas de capture d'écran (aucun outil de screenshot navigateur
disponible dans cet environnement) — rendu visuel à valider par l'utilisateur.
