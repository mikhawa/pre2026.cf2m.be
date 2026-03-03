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
├── Security/         # Voters custom
└── EventListener/    # Listeners/Subscribers Doctrine et Kernel

templates/            # Twig
assets/               # JS (Stimulus controllers) + CSS
docs/                 # Documentation technique
.github/workflows/    # CI/CD GitHub Actions
```

## Sécurité & Authentification
- Symfony Security Component
- Voters custom pour les permissions granulaires
- Tokens pour confirmation email et reset password (voir `TokenService`)
- Roles : `ROLE_SUPER_ADMIN`, `ROLE_ADMIN`, `ROLE_USER`

## Frontend
- Pas de bundler (Webpack/Vite) — ImportMap natif Symfony
- Stimulus pour les composants JS interactifs
- Turbo pour la navigation SPA-like sans rechargement complet

## Back-office (EasyAdmin 4)
- Interface d'administration générée via EasyAdmin 4
- Un `CrudController` par entité dans `src/Controller/Admin/`
- `DashboardController` central : `src/Controller/Admin/DashboardController.php`
- Accès restreint aux rôles `ROLE_ADMIN` et `ROLE_SUPER_ADMIN` (via `security.yaml`)
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
