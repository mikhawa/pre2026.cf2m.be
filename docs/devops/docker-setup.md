# Environnement Docker — Développement

## Services (docker-compose.yml)
| Service | Image             | Port local | Rôle |
|---------|-------------------|------------|------|
| php | custom/php8.5-fpm | — | App Symfony |
| nginx | nginx:alpine      | 8085 | Reverse proxy |
| db | mariadb:11.4      | 3307 | Base de données |
| phpmyadmin | phpmyadmin:latest | 8181 | Interface de gestion BDD |
| mailer | mailpit           | 8025 | SMTP de test |

## Commandes quotidiennes
```bash
docker compose up -d
docker compose exec php bin/console [commande]
docker compose exec php composer install
docker compose exec php bin/console doctrine:migrations:migrate
```

## Variables d'environnement dev
Fichier `.env.local` (non commité) — voir `.env` pour le template

## Accès
- App : http://localhost:8085
- Mailpit : http://localhost:8025
- phpMyAdmin : http://localhost:8181 (user/pass dans .env.local)
- BDD : localhost:3307 (user/pass dans .env.local)