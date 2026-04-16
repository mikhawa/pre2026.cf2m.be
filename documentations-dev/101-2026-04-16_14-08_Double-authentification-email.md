# 101 — Double authentification par email

**Date** : 2026-04-16 14:08  
**Branche** : feature/12-authenticate-double

## Fichiers modifiés

- `src/Entity/User.php`
- `config/packages/security.yaml`

## Fichiers créés

- `src/Service/TwoFactorEmailService.php`
- `src/EventSubscriber/TwoFactorLoginSubscriber.php`
- `src/EventSubscriber/TwoFactorKernelSubscriber.php`
- `src/Controller/TwoFactorController.php`
- `templates/security/two_factor.html.twig`
- `templates/emails/two_factor_code.html.twig`
- `migrations/Version20260416140808.php`

## Résumé des changements

Mise en place d'une double authentification (2FA) par email pour les rôles ROLE_SUPER_ADMIN, ROLE_ADMIN et ROLE_PEDAGO.

Après la connexion email/mot de passe réussie, un code à 6 chiffres est envoyé à l'adresse email de l'utilisateur. Celui-ci doit le saisir sur la page `/double-authentification` dans les 15 minutes. Un bouton "Renvoyer le code" permet de régénérer un nouveau code. La session est marquée `2fa_verified = true` après validation ; ce flag est remis à zéro à la déconnexion.

## Raison

Renforcement de la sécurité pour les comptes à accès sensibles (administration, pédagogie).
