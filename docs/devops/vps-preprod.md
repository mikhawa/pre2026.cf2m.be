# Déploiement VPS — Préprod & Prod

## À faire plus tard

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
Trois workflows distincts dans `.github/workflows/` :

| Workflow | Déclenchement | Rôle |
|----------|----------------|------|
| `symfony.yml` | push/PR sur `main` | Tests PHPUnit uniquement (pas de déploiement) |
| `deploy-preprod.yml` | push sur `preprod/*` | Tests + déploiement SSH sur `pre2026.cf2m.be` |
| `deploy-prod.yml` | push sur `production` | Tests + déploiement SSH sur `production.cf2m.be` |

Étapes du pipeline de déploiement (preprod et prod) :
1. Checkout du dépôt
2. Installation PHP 8.5 via `shivammathur/setup-php@v2`
3. Cache Composer
4. `composer install`
5. Exécution des tests PHPUnit
6. SSH vers VPS (`appleboy/ssh-action`) → `git pull` + `composer install` + migrations + fixtures (preprod) + `cache:clear` + assets + permissions

Différences notables :
- **Preprod** (`--env=dev`) : charge les fixtures (`doctrine:fixtures:load --group=app`), répertoire codé en dur `/var/www/vhosts/cf2m.be/pre2026.cf2m.be`.
- **Prod** (`--env=prod`) : pas de fixtures, `composer install --no-dev --optimize-autoloader`, répertoire fourni par le secret `PROD_VPS_PATH`.

## Secrets GitHub requis
À configurer dans **Settings > Secrets and variables > Actions** du dépôt. Les deux workflows de déploiement (preprod et prod) partagent les mêmes secrets de connexion SSH :

| Secret | Description |
|--------|-------------|
| `PROD_VPS_HOST` | IP ou hostname du VPS |
| `PROD_VPS_USER` | Utilisateur SSH (utilisateur système Plesk dédié au domaine) |
| `PROD_SSH_PRIVATE_KEY` | Clé SSH privée pour accès au VPS |
| `PROD_VPS_PATH` | Chemin absolu du répertoire de déploiement (utilisé par `deploy-prod.yml` uniquement ; `deploy-preprod.yml` a son chemin codé en dur) |

## Déploiement manuel (si nécessaire)
```bash
ssh $PROD_VPS_USER@$PROD_VPS_HOST
# Préprod
cd /var/www/vhosts/cf2m.be/pre2026.cf2m.be
git pull origin preprod/<branche>
composer install
php bin/console doctrine:migrations:migrate --no-interaction --env=dev
php bin/console doctrine:fixtures:load --group=app --no-interaction --env=dev
php bin/console cache:clear --env=dev
php bin/console cache:warmup --env=dev

# Production
cd <PROD_VPS_PATH>
git pull origin production
composer install --no-dev --optimize-autoloader
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

## Configuration Symfony en production
Fichier `.env.local` sur le serveur (non versionné) :
```
APP_ENV=prod
APP_SECRET=<secret>
DATABASE_URL=mysql://user:pass@127.0.0.1:3306/cf2m?serverVersion=mariadb-11.4
MAILER_DSN=mailjet+api://...
```

## Configuration emails en mode dev sur le VPS

Quand `APP_ENV=dev` sur le serveur préprod (test, débogage), tous les emails sortants sont automatiquement redirigés vers `michaeljpitz@gmail.com` via `config/packages/dev/mailer.yaml`.

Cela concerne **tous les déclencheurs** : codes 2FA, préinscriptions, contact, bienvenue, reset mot de passe.

Pour repasser en mode production (vrais destinataires), mettre `APP_ENV=prod` dans `.env.local`.

## TODO (mise en production)
- [X] Créer et configurer tous les secrets GitHub
- [X] Ajouter l'étape SSH de déploiement dans le workflow (`appleboy/ssh-action` ou équivalent)
- [X] Configurer le virtualhost Nginx + PHP-FPM sur le VPS via Plesk
- [X] Mettre en place les sauvegardes automatiques de la BDD (Plesk ou cron)
- [X] Configurer le certificat SSL (Let's Encrypt via Plesk)
- [X] Séparer le workflow de tests du workflow de déploiement
