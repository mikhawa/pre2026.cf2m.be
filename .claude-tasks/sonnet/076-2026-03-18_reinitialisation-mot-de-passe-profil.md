---
modèle: sonnet
justification: Controller frontend + envoi email + validation token + formulaire — complexité métier moyenne
date: 2026-03-18
---

## Tâche 076 — Réinitialisation du mot de passe depuis le profil utilisateur

### Fonctionnalité
L'utilisateur connecté peut demander un lien de réinitialisation de mot de passe depuis sa page profil.
Le lien envoyé par email est valable 1 heure et redirige vers un formulaire sécurisé.

### Flux complet
1. Bouton "Modifier mon mot de passe" sur `/profil` → POST `/profil/demande-reinitialisation`
2. Génération d'un token (bin2hex random_bytes 32 = 64 chars), stocké en BDD avec timestamp
3. Email envoyé avec lien `GET /reinitialisation-mot-de-passe/{token}`
4. Formulaire de saisie (nouveau MDP + confirmation), CSRF protégé, token valide 1h
5. Après validation : MDP haché, token effacé, redirect vers connexion

### Fichiers modifiés
- `src/Repository/UserRepository.php` — ajout `findByResetToken()`
- `src/Controller/ProfileController.php` — constructeur + route `POST /profil/demande-reinitialisation`
- `src/Controller/SecurityController.php` — route `GET/POST /reinitialisation-mot-de-passe/{token}`
- `templates/profil/index.html.twig` — bouton "Modifier mon mot de passe"
- `templates/emails/reset_password.html.twig` — email avec lien de réinitialisation (nouveau)
- `templates/security/reset_password.html.twig` — formulaire de saisie du nouveau MDP (nouveau)

### Sécurité
- Token cryptographiquement sûr (random_bytes 32)
- Expiration 1h vérifiée côté serveur
- CSRF sur le formulaire (token lié au reset token)
- Token effacé après utilisation
- Validation MDP : min 8 chars, max 64 chars, confirmation identique
