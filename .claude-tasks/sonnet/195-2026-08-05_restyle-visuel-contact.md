# Tâche : Adaptation visuelle "raffiné white/dark" — page contact (formulaire réel + Turnstile)

**Numéro** : 195
**Date** : 2026-08-05
**Modèle utilisé** : Sonnet
**Justification du modèle** : Suite directe des tâches 192-194 (restyle CSS réutilisant l'architecture et les tokens déjà posés) — complexité faible.
**Complexité** : Faible
**Fichiers concernés** : `assets/styles/app.css`

## Contexte nécessaire
Étape 5 du plan de restyle "raffiné white/dark". `templates/contact/index.html.twig` utilise déjà un vrai formulaire Symfony (`ContactType`) avec honeypot anti-spam et widget Cloudflare Turnstile — contrairement au mockup qui ne référence qu'un `ContactType` fictif (champs `firstName`/`lastName`/`formation` en `EntityType`, sans Turnstile). Aucune reproduction de structure nécessaire : la page réelle est fonctionnellement complète, il s'agissait de vérifier l'héritage visuel des étapes précédentes et d'harmoniser les derniers écarts.

## Constat (vérifié par capture d'écran, dark + light, avant modification)
La page héritait déjà du rayon de carte agrandi (`.cf2m-card`, étape 3) et du style de titre de section. Deux écarts identifiés par rapport à la charte "pill" désormais établie sur tout le reste du site (hero, cartes formations, partenaires) :
- `.cf2m-input` (champs texte/textarea) : rayon `0.5rem`, plus carré que les autres composants.
- `.cf2m-btn-submit` ("Envoyer le message", et aussi "Retour à l'accueil" sur `contact/success.html.twig` qui partage la classe) : rayon `0.5rem`, seul bouton plein du site encore non-pill.

## Résultat
**`assets/styles/app.css`** :
- `.cf2m-input` : `border-radius: 0.5rem → 0.75rem`.
- `.cf2m-btn-submit` : `border-radius: 0.5rem → 999px` (pill) — bénéficie aussi à `contact/success.html.twig` sans modification supplémentaire (classe partagée).

Vérifié par capture d'écran (dark + light) sur `/contact` (formulaire, Turnstile en mode test — widget "Succès !"/"Vérification…" visible, confirmant qu'il se charge correctement dans l'environnement de vérification) et `/contact/merci` (page de remerciement). Aucune régression, page `200`.

## Suite
Étapes 6 à 7 du plan restent à faire (login avec Turnstile + liens forgot-password/register, vérification visuelle des pages non couvertes) — voir mémoire projet associée.
