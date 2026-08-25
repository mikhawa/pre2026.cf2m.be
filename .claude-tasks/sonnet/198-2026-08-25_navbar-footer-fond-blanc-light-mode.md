# Tâche : Navbar + footer en fond blanc en mode clair

**Numéro** : 198
**Date** : 2026-08-25
**Modèle utilisé** : Sonnet
**Justification du modèle** : Modification CSS/Twig ciblée sur templates existants, cohérente avec les tâches de restyle précédentes (192-197) — pas d'architecture ni de sécurité impliquée.
**Complexité** : Moyenne
**Fichiers concernés** : `assets/styles/app.css`, `templates/base.html.twig`, `.claude/memory/navbar-convention.md`

## Contexte nécessaire
Convention précédente ([[navbar-convention]]) : navbar et footer restaient volontairement sur fond sombre même en light mode. L'utilisateur a demandé explicitement de renverser cette décision.

## Objectif
En `[data-theme="light"]`, la navbar et le footer passent en fond blanc (au lieu de sombre), avec bascule du logo (blanc → bleu) et adaptation des couleurs de texte/accent pour rester lisibles.

## Modifications apportées
- `templates/base.html.twig` : ajout d'un second `<img>` (logo bleu `logo-cf2m-bleu.svg`) dans la navbar et le footer, à côté du logo blanc existant, classes `cf2m-logo-img--dark` / `cf2m-logo-img--light` (même pattern que la page login).
- `assets/styles/app.css` :
  - Bascule d'affichage des logos (`.cf2m-logo-img--light { display:none }` par défaut, inversé sous `[data-theme="light"]`).
  - Remplacement du bloc `[data-theme="light"] .cf2m-navbar/.cf2m-footer` (qui forçait fond sombre + texte blanc) par un bloc fond blanc + texte foncé (navbar, dropdowns desktop/mobile, footer, bouton thème).
  - Ajustement des séparateurs du menu mobile collapsed pour rester visibles sur fond blanc.
- `.claude/memory/navbar-convention.md` : mémoire mise à jour pour documenter le revirement.

## Revue design-review (agent) et correctifs
L'agent `design-review` a été lancé sur le diff et a remonté 4 problèmes, tous corrigés :
1. Bouton bascule thème **mobile** (`.cf2m-theme-toggle-mobile`) invisible en light mode (texte blanc sur navbar blanche) → ajout d'une surcharge `[data-theme="light"] .cf2m-theme-toggle-mobile` (miroir de la version desktop).
2. Bordures de `.cf2m-btn-connexion` et `.cf2m-user-dropdown-toggle` non adaptées (quasi-blanches sur fond blanc) → `border-color` ajouté dans les surcharges light.
3. Hover/focus des `.cf2m-dropdown-item`/`.cf2m-dropdown-all` cassé par la règle générique `[data-theme="light"] .cf2m-navbar a { color: ... !important; }` (plus spécifique/important) → sélecteur restreint via `:not(.cf2m-dropdown-item):not(.cf2m-dropdown-all)`.
4. Contraste du `.cf2m-footer .text-muted` insuffisant (≈3,9:1) et incohérent selon la page (une règle générique `body:not(.page-home):not(.page-login) .text-muted` plus spécifique écrasait la couleur) → ajout d'une surcharge dédiée `#4a6070` avec sélecteur assez spécifique pour gagner sur toutes les pages.

En creusant le point 3, un bug analogue non détecté par l'agent a été trouvé et corrigé : le hover cyan de `.cf2m-btn-connexion` et `.cf2m-user-dropdown-toggle` était écrasé par la règle de couleur de base en light mode (même spécificité, ordre de source défavorable) → ajout de règles `:hover`/`[aria-expanded="true"]` dédiées en light mode pour ces deux composants.

## Critères d'acceptation
- [x] `bin/console lint:twig` OK sur `base.html.twig`
- [x] CSS : accolades équilibrées (vérif automatique)
- [x] Revue par l'agent `design-review` + correction des 4 points remontés
- [ ] Vérification visuelle manuelle en environnement Docker (recommandée avant merge — bascule du thème sur la home, une page intérieure, mobile et desktop)

## Résultat
Navbar et footer basculent en fond blanc avec logo bleu et texte foncé quand le thème clair est actif ; le dark mode reste inchangé (fond sombre, logo blanc). Mémoire projet mise à jour pour refléter ce nouveau comportement. `public/assets/` (build AssetMapper, gitignoré) vidé à la demande de l'utilisateur pour forcer la régénération.
