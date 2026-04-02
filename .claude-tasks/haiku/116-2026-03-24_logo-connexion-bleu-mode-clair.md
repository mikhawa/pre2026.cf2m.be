# 116 — Logo connexion : version foncée en mode clair

**Modèle** : Haiku
**Justification** : Changement visuel simple, CSS + Twig

## Changement
En mode clair sur la page de connexion, le logo blanc central (`logo-cf2m.svg`) est remplacé par le logo bleu/foncé (`logo-cf2m-bleu.svg`).

## Fichiers modifiés
- `datas/LOGO CF simple bleu.svg` → copié vers `public/images/logo-cf2m-bleu.svg`
- `templates/security/login.html.twig` — deux `<img>` avec classes `--dark` et `--light`
- `assets/styles/app.css` — CSS display swap via `[data-theme="light"]`

## Mécanisme
Deux `<img>` superposées : `cf2m-login-logo--dark` visible par défaut, masquée en light mode ; `cf2m-login-logo--light` masquée par défaut, visible en light mode.
