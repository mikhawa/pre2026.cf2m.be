# 033 — Image de fond hero + renforcement glassmorphisme

**Modèle** : Sonnet
**Justification** : Modification CSS frontend — complexité modérée (correction CSS + ajustements visuels)
**Date** : 2026-03-09

## Fichiers modifiés

- `public/images/hero-bg.jpg` — copie de `datas/jeune-homme-etudiant-dans-la-bibliotheque-universitaire.jpg`
- `assets/styles/app.css` — correction + amélioration hero + glassmorphisme

## Résumé

1. **Image de fond** : Copie de l'image `datas/jeune-homme-etudiant-dans-la-bibliotheque-universitaire.jpg` vers `public/images/hero-bg.jpg` (chemin déjà référencé dans le CSS).

2. **Correction CSS** : La propriété `background-image` contenait une syntaxe invalide (`url(...) center / cover no-repeat`). Corrigé en séparant les propriétés : `background-size`, `background-position`, `background-repeat` avec les valeurs correctes pour chaque couche.

3. **Overlay renforcé** : L'overlay `::before` passe de 52%→30% à 72%→30% d'opacité pour garantir la lisibilité sur la photo lumineuse de bibliothèque.

4. **Glassmorphisme amélioré** :
   - `backdrop-filter: blur(32px) saturate(1.8) brightness(0.92)` (au lieu de blur(20px) saturate(1.5))
   - Fond légèrement plus opaque : `rgba(5, 12, 28, 0.48)`
   - Border-radius agrandi : `1.75rem`
   - Box-shadow multicouche avec lueur cyan subtile
   - Meilleur rendu sur mobile avec opacité augmentée
