# 073 — Réinitialisation du mot de passe depuis le profil utilisateur

**Date** : 2026-03-18 16:00
**Branche** : `feature/add-icones-into-admin`

## Besoin
L'utilisateur connecté doit pouvoir modifier son mot de passe depuis son profil,
via un lien sécurisé envoyé par email.

## Flux implémenté

```
/profil → bouton "Modifier mon mot de passe"
    → POST /profil/demande-reinitialisation (ProfileController, connecté)
        → génère token 64 chars, stocke en BDD avec timestamp
        → envoie email avec lien
        → flash + redirect /profil

Email → lien GET /reinitialisation-mot-de-passe/{token} (SecurityController, public)
    → vérifie token (existe + < 1h)
    → affiche formulaire (nouveau MDP + confirmation)

POST /reinitialisation-mot-de-passe/{token}
    → valide CSRF, longueur (8-64), correspondance
    → hache et sauvegarde le nouveau MDP
    → efface token + timestamp
    → flash + redirect /connexion
```

## Fichiers modifiés/créés

| Fichier | Action |
|---|---|
| `src/Repository/UserRepository.php` | Ajout `findByResetToken()` |
| `src/Controller/ProfileController.php` | Constructeur MailerInterface + route `POST /profil/demande-reinitialisation` |
| `src/Controller/SecurityController.php` | Imports + route `GET/POST /reinitialisation-mot-de-passe/{token}` |
| `templates/profil/index.html.twig` | Bouton "Modifier mon mot de passe" avec confirmation JS |
| `templates/emails/reset_password.html.twig` | Nouveau — email avec lien de réinitialisation |
| `templates/security/reset_password.html.twig` | Nouveau — formulaire de saisie du nouveau MDP |

## Sécurité
- Token : `bin2hex(random_bytes(32))` = 64 chars hexadécimaux (cryptographiquement sûr)
- Expiration : 1 heure (vérifié côté serveur)
- CSRF : token lié au reset token (`reset_password_{token}`)
- Token effacé de la BDD après utilisation
- Validation : min 8 chars, max 64 chars (limite bcrypt), confirmation identique
- L'entité `User` dispose déjà des champs `resetPasswordToken` et `resetPasswordRequestedAt`
