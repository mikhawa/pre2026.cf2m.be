# 147 — Navbar sticky sur toutes les pages

**Modèle** : Haiku
**Justification** : Modification CSS pure + ajustements de templates Twig simples
**Date** : 2026-04-22

## Problème

La navbar était en `position: absolute` sur la home page et les pages login/register, ce qui la faisait disparaître au scroll. Elle était sticky uniquement sur les pages intérieures.

## Solution

Rendre la navbar sticky sur toutes les pages, en mode sombre comme en mode clair.

## Fichiers modifiés

- `assets/styles/app.css`
  - Ajout de `position: sticky; top: 0; z-index: 1030` dans la règle de base `.cf2m-navbar`
  - Suppression du bloc `position: absolute` pour `.page-home .cf2m-navbar, .page-login .cf2m-navbar`
  - Suppression du bloc sticky redondant `body:not(.page-home):not(.page-login) .cf2m-navbar`
  - Suppression du `padding-top: calc(70px + 4rem)` dans `.cf2m-hero` → remplacé par `padding: 4rem 0 5rem`
- `templates/security/login.html.twig`
  - Suppression `padding-top: 70px`, `min-height: 100vh` → `min-height: calc(100vh - 70px)`
- `templates/security/reset_password.html.twig` — idem
- `templates/security/two_factor.html.twig` — idem
- `templates/registration/register.html.twig` — idem

## Résumé

La navbar a maintenant `position: sticky` universellement. Le hero et les pages login n'ont plus besoin de compenser la hauteur de navbar car elle est dans le flux normal du document.
