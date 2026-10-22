# 205 — Polices Poppins (titres) et Montserrat (sous-titres, descriptions)

**Modèle utilisé** : Haiku

**Justification** : remplacement de polices limité aux variables CSS existantes et à l'URL Google Fonts,
sans logique métier ni changement de composant.

## Contexte

La maquette Figma demande **Poppins** pour les titres et **Montserrat** pour les sous-titres et les
descriptions. Le changement s'applique à tout le site : Outfit → Poppins, DM Sans → Montserrat.
Les anciennes polices ne sont plus chargées.

## Fichiers modifiés

- `templates/base.html.twig` — URL Google Fonts (lien non bloquant + `<noscript>`) : Poppins 400–900, Montserrat 400–700 + 400 italique
- `assets/styles/app.css` — `--font-display` → Poppins, `--font-body` → Montserrat
- `assets/styles/tokens.css` — `--cf2m-font-display` → Poppins, nouveau token `--cf2m-font-text` (Montserrat)
- `assets/styles/layout.css` — accroche du logo, sous-titre et accroche du hero passés sur `--cf2m-font-text`
- `docs/architecture/frontend.md` — ligne « Polices » mise à jour

## Résumé

Les composants utilisaient déjà des variables CSS. Il a donc suffi de changer les tokens de police et
l'URL de chargement. Les titres, les chiffres du hero, les liens du menu et le bouton restent sur
la police d'affichage (Poppins). Les sous-titres et descriptions utilisent `--cf2m-font-text` (Montserrat).
Les maquettes de référence dans `datas/` ne sont pas modifiées.

## Vérification

- `grep -rn "Outfit\|DM Sans\|DM+Sans" assets templates docs/architecture` ne doit rien renvoyer.
- DevTools > Computed > « Rendered Fonts » : Poppins sur les titres, Montserrat sur `body` et le sous-titre du hero.
- Contrôler les graisses 800/900 et le retour à la ligne de l'en-tête sur mobile : ces polices sont plus larges que les précédentes.
