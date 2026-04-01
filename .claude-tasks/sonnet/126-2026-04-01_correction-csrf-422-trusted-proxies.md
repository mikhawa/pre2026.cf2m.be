# Tâche 126 — Correction erreur 422 sur le formulaire de profil (production)

**Date** : 2026-04-01  
**Modèle** : Sonnet  
**Justification** : Diagnostic de sécurité / configuration infrastructure prod

## Problème

Le formulaire `/profil/modifier` retournait systématiquement une erreur **HTTP 422** en production, jamais en dev.

## Cause racine

Plesk (VPS Debian 12.13) agit comme reverse proxy et termine le SSL. Symfony reçoit les requêtes en HTTP interne (`$request->isSecure() = false`).

Le mécanisme `SameOriginCsrfTokenManager` (double-submit cookie) diverge alors entre client et serveur :

| Côté | Logique | Nom du cookie cherché |
|------|---------|----------------------|
| Navigateur (HTTPS) | `window.location.protocol === 'https:'` | `__Host-submit_<TOKEN>` |
| Symfony (HTTP interne) | `$request->isSecure() === false` | `submit_<TOKEN>` |

→ **Mismatch** → token CSRF invalide → **422 Unprocessable Entity**

## Correction

**Fichier modifié** : `config/packages/framework.yaml`

Ajout d'un bloc `when@prod` avec :
```yaml
trusted_proxies: '127.0.0.1'
trusted_headers: ['x-forwarded-for', 'x-forwarded-proto', 'x-forwarded-port']
```

Symfony lit désormais `X-Forwarded-Proto: https` depuis Apache/Plesk → `$request->isSecure() = true` → le cookie `__Host-` est correctement cherché.

## Impact

- Tous les formulaires en production (pas seulement le profil) bénéficient du fix.
- Aucun impact sur dev (bloc `when@prod` uniquement).
- Aucun impact sur les tests (bloc `when@test` inchangé).
