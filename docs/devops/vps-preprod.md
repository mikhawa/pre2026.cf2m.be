# Déploiement VPS — Préprod & Prod

## Infrastructure
| | Préprod | Prod |
|-|---------|------|
| OS | Debian 12.13 | Debian 12.13 |
| Gestionnaire | Plesk | Plesk |
| Serveur web | Nginx | Nginx |
| PHP | 8.5-FPM | 8.5-FPM |
| BDD | MariaDB 11.4 | MariaDB 11.4 |
| Répertoire | `/var/www/preprod/cf2m` | `/var/www/cf2m` |

## Déploiement automatisé (GitHub Actions)
Voir `.github/workflows/symfony.yml` et `docs/devops/github-actions.md`.

Déclenchement : push ou PR sur `main`.

Étapes du pipeline :
1. Checkout du dépôt
2. Installation PHP 8.5 via `shivammathur/setup-php@v2`
3. Cache Composer
4. `composer install --no-dev --optimize-autoloader`
5. Exécution des tests PHPUnit
6. SSH vers VPS → `git pull` + migrations + `cache:clear`

## Secrets GitHub requis
À configurer dans **Settings > Secrets and variables > Actions** du dépôt :

| Secret | Description |
|--------|-------------|
| `SSH_PRIVATE_KEY` | Clé SSH privée pour accès au VPS |
| `VPS_HOST` | IP ou hostname du VPS |
| `VPS_USER` | Utilisateur SSH |
| `DATABASE_URL` | DSN MariaDB préprod |
| `APP_SECRET` | Clé secrète Symfony |
| `MAILER_DSN` | DSN du service mail |

## Déploiement manuel (si nécessaire)
```bash
ssh user@vps-host
cd /var/www/preprod/cf2m
git pull origin main
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## Configuration Symfony en production
Fichier `.env.local` sur le serveur (non versionné) :
```
APP_ENV=prod
APP_SECRET=<secret>
DATABASE_URL=mysql://user:pass@127.0.0.1:3306/cf2m?serverVersion=mariadb-11.4
MAILER_DSN=smtp://...
```

## TODO (mise en production)
- [ ] Créer et configurer tous les secrets GitHub
- [ ] Ajouter l'étape SSH de déploiement dans le workflow (`appleboy/ssh-action` ou équivalent)
- [ ] Configurer le virtualhost Nginx + PHP-FPM sur le VPS via Plesk
- [ ] Mettre en place les sauvegardes automatiques de la BDD (Plesk ou cron)
- [ ] Configurer le certificat SSL (Let's Encrypt via Plesk)
- [ ] Séparer le workflow de tests du workflow de déploiement
