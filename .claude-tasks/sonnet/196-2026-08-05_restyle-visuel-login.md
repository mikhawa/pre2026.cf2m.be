# Tâche : Adaptation visuelle "raffiné white/dark" — login (formulaire réel + Turnstile + liens forgot-password/register)

**Numéro** : 196
**Date** : 2026-08-05
**Modèle utilisé** : Sonnet
**Justification du modèle** : Suite directe des tâches 192-195 (restyle CSS réutilisant l'architecture et les tokens déjà posés) — complexité faible.
**Complexité** : Faible
**Fichiers concernés** : `assets/styles/app.css`

## Contexte nécessaire
Étape 6 du plan de restyle "raffiné white/dark". `templates/security/login.html.twig` utilise déjà un formulaire Symfony réel (routes `app_login`/`app_forgot_password`/`app_register`, CSRF, Turnstile) avec un composant `.cf2m-login-*` (carte glassmorphisme, champs à icône, bouton, liens). Vérification préalable : `templates/security/forgot_password.html.twig`, `templates/registration/register.html.twig`, `templates/security/reset_password.html.twig` et `templates/security/two_factor.html.twig` réutilisent **toutes** le même composant `.cf2m-login-*` — un seul jeu de règles CSS couvre donc tout le parcours d'authentification.

## Constat (vérifié par capture d'écran, dark + light, avant modification)
Page déjà très proche de l'esthétique du mockup (carte centrée, logo, glassmorphisme, champs à icône). Seul écart avec la charte "pill" désormais posée aux étapes 3-5 (accueil, fiche formation, contact) : `.cf2m-login-input` et `.cf2m-login-btn` en `0.6rem` (rayon carré), `.cf2m-login-error` idem.

## Résultat
**`assets/styles/app.css`** :
- `.cf2m-login-input` : `border-radius: 0.6rem → 0.75rem`.
- `.cf2m-login-error` : `border-radius: 0.6rem → 0.75rem`.
- `.cf2m-login-btn` : `border-radius: 0.6rem → 999px` (pill).

Grâce au composant partagé, ces trois changements s'appliquent automatiquement à `/connexion`, `/mot-de-passe-oublie`, `/inscription`, reset-password et 2FA. Vérifié par capture d'écran sur les trois premières pages (dark) : rendu cohérent, boutons pill lumineux (glow cyan existant conservé), aucune régression, pages `200`.

## Suite
Étape 7 du plan reste à faire : vérification visuelle des pages non couvertes (profil, works, page, reset-password/2FA en détail) — voir mémoire projet associée.
