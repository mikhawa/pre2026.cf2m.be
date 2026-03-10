# 035 — Audit tests & sécurité générale

**Date** : 2026-03-10
**Modèle** : Sonnet
**Justification** : Audit multi-fichiers, corrections de sécurité (security.yaml + entité + dépendance)

## Fichiers modifiés
- `config/packages/security.yaml` — ajout `login_throttling`
- `src/Entity/ContactMessage.php` — ajout contraintes `Assert\Length`
- `composer.json` + `composer.lock` — ajout `symfony/rate-limiter`

## Résumé

### Tests unitaires
88/88 tests passent sans régression.

### Audit `composer audit`
Aucune vulnérabilité connue dans les dépendances.

### Corrections appliquées

#### 🔴 Brute-force login (critique)
Installé `symfony/rate-limiter` et ajouté `login_throttling` dans le firewall `main` :
- 5 tentatives max par tranche de 5 minutes
- Bloque automatiquement par IP + email

#### 🟡 Contraintes de longueur (ContactMessage)
Ajout de `Assert\Length` sur les 4 champs publics du formulaire de contact :
- `nom` : max 100 car.
- `email` : max 180 car.
- `sujet` : max 255 car.
- `message` : max 3000 car. (TEXT sans limite DB → risque DoS corrigé)

### Points sains confirmés
- CSRF activé (form_login + SameOriginCsrfTokenManager)
- Double protection admin (access_control + IsGranted)
- Hachage MDP via algorithme `auto`
- Honeypot formulaire contact
- QueryBuilder uniquement (zéro SQL natif)
- `declare(strict_types=1)` partout
- `eraseCredentials()` correctement implémenté

### Points à surveiller (non bloquants)
- `|raw` sur `works.description`, `formation.description`, `page.content` : acceptable car saisie réservée aux super-admins via EasyAdmin
- `APP_SECRET` dans `.env` committé : dev uniquement, ne jamais réutiliser en prod
