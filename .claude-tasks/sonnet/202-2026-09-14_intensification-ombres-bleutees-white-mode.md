# 202 — Intensification des ombres bleutées du fond white mode

**Modèle utilisé** : Sonnet

**Justification** : ajustement de valeurs CSS, continuité directe de la tâche 201.

## Contexte

L'utilisateur a signalé ne pas voir les ombres bleutées ajoutées en tâche 201 sur le fond
white mode. Vérification faite via le CSS effectivement servi (`curl` sur le digest
AssetMapper courant) : le dégradé était bien présent et appliqué, mais ses opacités
(0.05 à 0.07) étaient trop faibles pour être perceptibles sur un fond quasi-blanc
(`#f5f7f9`) — contrairement au dark mode où les mêmes opacités sont bien visibles car
appliquées sur un fond très sombre (`#050e18`).

## Fichiers modifiés

- `assets/styles/app.css` — `[data-theme="light"] body` : opacités des 3 halos remontées
  de 0.05-0.07 à 0.15-0.20, vignette basse de 0.45 à 0.60. Couleurs légèrement assombries
  en parallèle pour garder un rendu bleu net plutôt que délavé.

## Résumé

Sur fond blanc, un rgba à faible opacité se dilue presque totalement (contrairement à un
fond sombre où le même rgba ressort par contraste) — il fallait donc des opacités
nettement plus hautes que l'équivalent dark mode pour obtenir un résultat comparable à
l'œil. Calcul de vérification : `rgba(0,140,190,0.20)` sur `#f5f7f9` donne un mélange
≈ `#cce8f2`, un bleu clair mais net.

## Résultat

Vérifié par calcul de mélange alpha et par le CSS effectivement servi après recompilation
AssetMapper (digest changé, valeurs confirmées dans le fichier compilé). Pas de capture
d'écran possible (aucun outil de rendu navigateur dans cet environnement) — à confirmer
visuellement par l'utilisateur.
