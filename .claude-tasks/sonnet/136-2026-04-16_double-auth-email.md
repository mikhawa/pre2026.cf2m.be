# 136 — Double authentification par email (2FA)

**Modèle** : Sonnet  
**Justification** : Fonctionnalité de sécurité impliquant service, event subscribers, controller, migration et templates.  
**Date** : 2026-04-16  
**Branche** : feature/12-authenticate-double

## Fichiers modifiés

- `src/Entity/User.php` — ajout des champs `twoFactorCode` (string|null, 6) et `twoFactorCodeExpiresAt` (DateTimeImmutable|null) avec getters/setters

## Fichiers créés

- `src/Service/TwoFactorEmailService.php` — génération du code, envoi email, validation, constante `ROLES_REQUIRING_2FA`
- `src/EventSubscriber/TwoFactorLoginSubscriber.php` — intercepte `LoginSuccessEvent`, génère le code, redirige vers `/double-authentification`
- `src/EventSubscriber/TwoFactorKernelSubscriber.php` — intercepte `kernel.request`, redirige si 2FA non validé
- `src/Controller/TwoFactorController.php` — routes `app_two_factor` (GET/POST) et `app_two_factor_resend` (POST)
- `templates/security/two_factor.html.twig` — formulaire de saisie du code, style cohérent avec la page login
- `templates/emails/two_factor_code.html.twig` — email HTML avec le code mis en valeur
- `migrations/Version20260416140808.php` — `ALTER TABLE user ADD two_factor_code, two_factor_code_expires_at`
- `config/packages/security.yaml` — ajout `^/double-authentification: IS_AUTHENTICATED_FULLY` dans access_control

## Résumé

Double authentification par email pour les rôles ROLE_SUPER_ADMIN, ROLE_ADMIN et ROLE_PEDAGO.

**Flux :**
1. L'utilisateur saisit email + mot de passe → `form_login` valide normalement
2. `TwoFactorLoginSubscriber` intercepte `LoginSuccessEvent` : si rôle privilégié, génère un code à 6 chiffres (TTL 15 min), l'envoie par email, redirige vers `/double-authentification`
3. `TwoFactorKernelSubscriber` intercepte chaque requête : si l'utilisateur a un rôle privilégié et que `2fa_verified` != true en session, redirige vers `/double-authentification`
4. L'utilisateur saisit le code → validation par `TwoFactorEmailService::validateCode()` (hash_equals pour timing safety) → session `2fa_verified = true` → redirection vers `/profil`
5. Possibilité de renvoyer un nouveau code via le formulaire dédié

**Sécurité :**
- Code unique (effacé après validation)
- Comparaison `hash_equals` (timing-safe)
- Expiration 15 minutes
- CSRF sur les deux formulaires

## Résultat

Migration exécutée. Cache vidé. Services enregistrés (vérifiés via `debug:container` et `debug:event-dispatcher`).
