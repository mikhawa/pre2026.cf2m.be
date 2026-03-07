# 026 — Page de connexion et Symfony Security

**Date** : 2026-03-05 12:00
**Branche** : FirstFrontend

## Fichiers modifiés / créés

| Fichier | Changement |
|---------|-----------|
| `config/packages/security.yaml` | Provider Doctrine + form_login + logout |
| `src/Controller/SecurityController.php` | Nouveau contrôleur (login + logout) |
| `templates/security/login.html.twig` | Nouvelle page de connexion |
| `assets/styles/app.css` | Styles `.cf2m-login-*` (glassmorphisme) |
| `templates/base.html.twig` | Liens Connexion/Déconnexion fonctionnels |

## Raison

Création de la page de connexion utilisateur avec design cohérent avec le reste du site.

## Détails

### security.yaml — Provider Doctrine
```yaml
providers:
    app_user_provider:
        entity:
            class: App\Entity\User
            property: email

firewalls:
    main:
        form_login:
            login_path: app_login
            check_path: app_login
            enable_csrf: true
            default_target_path: app_home
        logout:
            path: app_logout
            target: app_home
```

### Routes créées
| Route | URL | Méthode |
|-------|-----|---------|
| `app_login` | `/connexion` | GET + POST |
| `app_logout` | `/deconnexion` | ANY (intercepté Symfony) |

### Design login
- Fond sombre avec blobs radial-gradient (cohérent avec hero)
- Carte glassmorphisme centrée (`backdrop-filter: blur(24px)`)
- Icônes SVG inline dans les champs
- Messages d'erreur traduits (`domaine: security`)
- Lien "Mot de passe oublié" (placeholder — à relier ultérieurement)

## Résultat

- GET `/connexion` → HTTP 200
- POST `/connexion` (mikhawa@cf2m.be / 123mikhawa) → HTTP 302 (connexion réussie)
