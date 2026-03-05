# 016 — Page de connexion et configuration Symfony Security

**Date** : 2026-03-05
**Modèle** : Sonnet
**Justification** : Controller + sécurité + template + CSS — tâche de niveau services/controllers

## Fichiers modifiés / créés

- `config/packages/security.yaml` — Provider Doctrine + form_login + logout configurés
- `src/Controller/SecurityController.php` — Routes `/connexion` et `/deconnexion`
- `templates/security/login.html.twig` — Page de connexion glassmorphisme
- `assets/styles/app.css` — Styles `.cf2m-login-*`
- `templates/base.html.twig` — Liens Connexion/Déconnexion actifs

## Résumé

### security.yaml
- Remplacement de `users_in_memory` par le provider Doctrine `app_user_provider` (entity: User, property: email)
- `form_login` configuré : check_path = login_path = `app_login`, CSRF activé, redirect vers `app_home`
- `logout` : path `app_logout`, target `app_home`

### SecurityController
- `GET /connexion` → `app_login` : redirige si connecté, sinon affiche le template
- `POST /connexion` → intercepté par Symfony Security
- `GET /deconnexion` → `app_logout` : intercepté par le firewall

### Template login.html.twig
- Design glassmorphisme cohérent avec le hero (backdrop-filter blur 24px)
- Champs email (_username) et mot de passe (_password)
- CSRF token intégré
- Affichage des erreurs d'authentification traduit via le domaine 'security'
- Lien "Mot de passe oublié" (placeholder)
- Lien retour accueil

## Résultat

Vérification :
- Routes : `app_login /connexion` et `app_logout /deconnexion` actives
- GET /connexion → HTTP 200
- POST /connexion (mikhawa@cf2m.be / 123mikhawa) → HTTP 302 (redirection succès)
