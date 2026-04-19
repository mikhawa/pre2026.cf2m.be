# 146 — Inscription utilisateur simple avec vérification email

**Modèle** : Sonnet
**Justification** : Controller + sécurité + formulaire + templates
**Date** : 2026-04-19
**Branche** : feature/15-inscription-simple-user

## Fichiers créés
- `src/Form/RegistrationType.php` — Formulaire : userName, email, plainPassword (RepeatedType)
- `src/Controller/RegistrationController.php` — Routes /inscription et /inscription/verification/{token}
- `src/Security/UserChecker.php` — Bloque login si activationToken présent (compte non activé)
- `templates/registration/register.html.twig` — Page d'inscription (style login)
- `templates/emails/registration_confirmation.html.twig` — Email de confirmation

## Fichiers modifiés
- `src/Repository/UserRepository.php` — Ajout `findByActivationToken()`
- `config/packages/security.yaml` — Ajout `user_checker: App\Security\UserChecker`
- `templates/security/login.html.twig` — Lien "S'inscrire" ajouté

## Résumé
- Inscription via /inscription : userName unique + email valide + mot de passe (8-64 car.)
- Status=0 + activationToken généré à l'inscription
- Email de confirmation envoyé avec lien /inscription/verification/{token}
- Vérification : status=1, activationToken=null, auto-login via Security::login()
- UserChecker bloque login si activationToken != null (rétro-compatible : comptes admin existants non affectés)

## Fichiers créés (complément)
- `src/EventSubscriber/StatusActivationSubscriber.php` — Passe status à 1 à la première connexion

## Résultat
Routes enregistrées, cache OK
