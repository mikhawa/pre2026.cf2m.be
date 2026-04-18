# Architecture globale — CF2m

## Stack technique
| Couche | Technologie |
|--------|-------------|
| Framework | Symfony 7.4 LTS (--webapp) |
| Langage | PHP 8.5 (strict_types) |
| Base de données | MariaDB 11.4 |
| Frontend | ImportMap + Stimulus + Turbo (pas de build step) |
| Back-office | EasyAdmin 4 |
| Containerisation | Docker (dev) |
| CI/CD | GitHub Actions |
| Hébergement | VPS Debian 12.13 sous Plesk (prod) |

## Environnements
```
dev (Docker local) → préprod (GitHub CI + VPS) → prod (VPS Debian 12.13 Plesk)
```

## Pattern architectural
- **MVC** : Controllers fins, logique métier dans les Services
- **Couche Service** : `src/Service/` — un service par domaine fonctionnel
- **Repository pattern** : `src/Repository/` — repositories custom étendant `ServiceEntityRepository`
- **Entités Doctrine** : attributs PHP 8 (pas d'annotations YAML/XML)

## Structure des dossiers clés
```
src/
├── Controller/
│   └── Admin/        # CrudControllers EasyAdmin (un par entité)
├── Entity/           # Entités Doctrine
├── Repository/       # Repositories custom
├── Service/          # Logique métier (voir .claude/CONTEXT.md pour le détail)
├── Form/             # FormTypes Symfony
├── Security/
│   └── Voter/        # Voters custom (FormationVoter, WorksVoter, ContentManagerVoter)
└── EventSubscriber/  # Subscribers Kernel et Sécurité (Turnstile, 2FA)

templates/            # Twig
assets/               # JS (Stimulus controllers) + CSS
docs/                 # Documentation technique
.github/workflows/    # CI/CD GitHub Actions
```

## Sécurité & Authentification
- Symfony Security Component
- Voters custom pour les permissions granulaires (`src/Security/Voter/`)
- Tokens pour confirmation email et reset password
- Double authentification (2FA) par email pour les rôles privilégiés (voir ci-dessous)

### Hiérarchie des rôles
| Rôle | Hérite de |
|---|---|
| `ROLE_SUPER_ADMIN` | `ROLE_ADMIN`, `ROLE_FORMATEUR`, `ROLE_STAGIAIRE`, `ROLE_USER` |
| `ROLE_ADMIN` | `ROLE_FORMATEUR`, `ROLE_STAGIAIRE`, `ROLE_USER` |
| `ROLE_PEDAGO` | `ROLE_FORMATEUR`, `ROLE_STAGIAIRE`, `ROLE_USER` |
| `ROLE_FORMATEUR` | `ROLE_STAGIAIRE`, `ROLE_USER` |
| `ROLE_STAGIAIRE` | `ROLE_USER` |

### Double authentification (2FA)
Les rôles `ROLE_SUPER_ADMIN`, `ROLE_ADMIN` et `ROLE_PEDAGO` sont soumis à une vérification par code email à chaque connexion.

**Flux :**
1. Connexion email/mot de passe réussie → `TwoFactorLoginSubscriber` (écoute `LoginSuccessEvent`)
2. Code à 6 chiffres généré, persisté en base, envoyé par email (TTL 15 min)
3. Redirection vers `/double-authentification` — l'accès au reste du site est bloqué par `TwoFactorKernelSubscriber` (écoute `kernel.request`)
4. Saisie du code → validation (`hash_equals`) → session `2fa_verified = true` → accès accordé

**Fichiers clés :**
- `src/Service/TwoFactorEmailService.php` — génération, envoi, validation
- `src/EventSubscriber/TwoFactorLoginSubscriber.php` — interception post-login
- `src/EventSubscriber/TwoFactorKernelSubscriber.php` — garde sur chaque requête
- `src/Controller/TwoFactorController.php` — routes `app_two_factor` + `app_two_factor_resend`
- `templates/security/two_factor.html.twig`
- `templates/emails/two_factor_code.html.twig`

## Frontend
- Pas de bundler (Webpack/Vite) — ImportMap natif Symfony
- Stimulus pour les composants JS interactifs
- Turbo pour la navigation SPA-like sans rechargement complet

## Back-office (EasyAdmin 4)
- Interface d'administration générée via EasyAdmin 4
- Un `CrudController` par entité dans `src/Controller/Admin/`
- `DashboardController` central : `src/Controller/Admin/DashboardController.php`
- Accès restreint aux rôles `ROLE_FORMATEUR`, `ROLE_ADMIN` et `ROLE_SUPER_ADMIN` (via `security.yaml`)
- Route du back-office : `/admin`
- Personnalisations à documenter dans `docs/architecture/easyadmin.md`

## API
- Routes préfixées `/api/`
- Langue : site en `fr` uniquement (base Symfony en `en`)

## Fichiers de référence
- Services détaillés : `.claude/CONTEXT.md`
- Schéma BDD : `docs/architecture/database-schema.md`
- Back-office EasyAdmin : `docs/architecture/easyadmin.md`
- Docker dev : `docs/devops/docker-setup.md`
- Déploiement VPS : `docs/devops/vps-preprod.md`
- GitHub Actions : `docs/devops/github-actions.md`
