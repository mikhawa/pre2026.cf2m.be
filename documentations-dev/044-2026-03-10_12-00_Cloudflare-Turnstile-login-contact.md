# 044 — Cloudflare Turnstile sur connexion et contact

**Date** : 2026-03-10 12:00
**Fichiers modifiés** :
- `src/Service/TurnstileVerifier.php` (nouveau)
- `src/EventSubscriber/TurnstileLoginSubscriber.php` (nouveau)
- `config/packages/twig.yaml`
- `.env`
- `templates/security/login.html.twig`
- `templates/contact/index.html.twig`
- `src/Controller/ContactController.php`

## Résumé

Intégration de Cloudflare Turnstile (alternative CAPTCHA sans friction) sur le formulaire de connexion et le formulaire de contact.

## Architecture

### Service `TurnstileVerifier`
Vérifie les tokens Turnstile via l'API `siteverify` de Cloudflare.
En cas d'indisponibilité réseau de l'API, retourne `true` pour ne pas pénaliser les utilisateurs légitimes.

### EventSubscriber `TurnstileLoginSubscriber`
S'accroche à `CheckPassportEvent` (priorité 0) — s'exécute **avant** la vérification du mot de passe.
Si le token Turnstile est invalide, lève une `CustomUserMessageAuthenticationException` dont le message apparaît dans la carte d'erreur du formulaire de connexion.

### Formulaire de contact
Le token est vérifié directement dans `ContactController::index()` après validation du formulaire.
En cas d'échec : message flash d'erreur + re-rendu du formulaire.

## Configuration des clés

Le fichier `.env` contient des **clés de test** (toujours valides) :
```env
TURNSTILE_SITE_KEY=1x00000000000000000000AA
TURNSTILE_SECRET_KEY=1x0000000000000000000000000000000AA
```

**En production** : créer les clés sur https://dash.cloudflare.com/ → Turnstile et les placer dans `.env.local` du VPS.

## Widget HTML
```html
<div class="cf-turnstile"
     data-sitekey="{{ turnstile_site_key }}"
     data-theme="dark|light"
     data-language="fr"></div>
```

La `turnstile_site_key` est exposée via une variable globale Twig dans `config/packages/twig.yaml`.
