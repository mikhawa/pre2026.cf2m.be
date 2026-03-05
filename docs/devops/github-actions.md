# GitHub Actions — Stratégie de déploiement

## À faire plus tard (améliorations possibles)

## Branches
- `main` → déploiement automatique en préprod
- `develop` → tests CI uniquement
- `feature/*` → tests CI uniquement

## Workflow préprod (.github/workflows/deploy-preprod.yml)
Étapes :
1. Checkout + cache Composer
2. composer install --no-dev --optimize-autoloader
3. Tests PHPUnit
4. SSH vers VPS → git pull + migrations + cache:clear
5. Notification (optionnel)

## Secrets GitHub requis
- SSH_PRIVATE_KEY
- VPS_HOST, VPS_USER
- DATABASE_URL_PREPROD

## Serveur cible
Ubuntu 22.04 — Nginx — PHP 8.5-FPM — MariaDB
Répertoire : /var/www/preprod/[projet]
