---
name: navbar-convention
description: La navbar et le footer passent en fond blanc en light mode (depuis 2026-08-25) — logo bascule blanc/bleu, texte foncé ; en dark mode ils restent sur fond sombre uniforme
metadata:
  type: project
---

## Convention navbar (couleur uniforme, dark mode)
Sur les pages intérieures (non-home, non-login), la navbar doit avoir `background: rgba(6, 14, 26, 0.80)` (équivalent `var(--cf2m-dark)`) + `backdrop-filter: none` en dropdown mobile pour rester visuellement identique à son apparence sur la home. Ne jamais laisser le `backdrop-filter` actif sur fond blanc.

## Revirement : navbar + footer en light mode (2026-08-25)
Décision précédente abandonnée à la demande explicite de l'utilisateur : la navbar et le footer passent désormais en **fond blanc** quand `[data-theme="light"]` est actif (au lieu de rester sombres). Implémentation dans `assets/styles/app.css` (bloc `Light mode : navbar + footer passent en clair`, ~ligne 1802+) et `templates/base.html.twig` :
- `.cf2m-navbar` / `.cf2m-footer` : `background: #ffffff` (ou proche), bordure/ombre légère pour la séparation (plus de `--cf2m-dark`).
- Logo : deux `<img>` par emplacement (navbar + footer), classes `cf2m-logo-img--dark` (logo blanc, `logo-cf2m-blanc.svg`, affiché par défaut) et `cf2m-logo-img--light` (logo bleu, `logo-cf2m-bleu.svg`, affiché via `[data-theme="light"] .cf2m-logo-img--light { display: inline-block; }`), même pattern que `cf2m-login-logo--light/--dark` sur la page login.
- Texte/liens navbar, dropdowns et footer : couleurs foncées (`#1a2b3c`, `#2a3f52`, `#4a6070`) au lieu de blanc/rgba. L'accent `--cf2m-cyan` n'est plus retinté pour ces zones — il utilise directement la valeur globale du light mode (`#0072a3`, définie sur `[data-theme="light"]` racine).

Si une future demande veut revenir à une navbar/footer toujours sombres, il faut restaurer l'ancien bloc (voir historique git de `app.css` avant ce commit) et retirer les seconds `<img>` de `base.html.twig`.
