# 036 — Intégration Cloudflare Turnstile

**Date** : 2026-03-10
**Modèle** : Sonnet
**Justification** : Service + EventSubscriber + templates — complexité métier multi-couches

## Fichiers créés
- `src/Service/TurnstileVerifier.php`
- `src/EventSubscriber/TurnstileLoginSubscriber.php`

## Fichiers modifiés
- `config/packages/twig.yaml` — global `turnstile_site_key`
- `.env` — `TURNSTILE_SITE_KEY` et `TURNSTILE_SECRET_KEY` (clés de test)
- `templates/security/login.html.twig` — script + widget
- `templates/contact/index.html.twig` — script + widget + flash erreur
- `src/Controller/ContactController.php` — injection TurnstileVerifier + vérification

## Architecture

### TurnstileVerifier (service)
- Appel HTTP POST vers `https://challenges.cloudflare.com/turnstile/v0/siteverify`
- Secret key injectée via `#[Autowire(env: 'TURNSTILE_SECRET_KEY')]`
- En cas d'erreur réseau : `return true` pour ne pas bloquer les utilisateurs légitimes

### TurnstileLoginSubscriber (EventSubscriber)
- Écoute `CheckPassportEvent` (priorité 0)
- S'exécute **avant** `CheckCredentialsListener` → économise une requête BDD sur les bots
- Filtre par route `app_login` uniquement
- Jette `CustomUserMessageAuthenticationException` si token invalide (message affiché dans la carte d'erreur du login)

### ContactController
- Token récupéré via `$request->request->get('cf-turnstile-response', '')`
- Si invalide : flash 'error' + re-rendu du formulaire (sans redirection)

## Configuration requise en production
```env
# Dans .env.local sur le VPS
TURNSTILE_SITE_KEY=votre_vraie_site_key
TURNSTILE_SECRET_KEY=votre_vraie_secret_key
```
Générer les clés sur : https://dash.cloudflare.com/ → Turnstile

## Tests
88/88 passent. Service correctement autowired.
