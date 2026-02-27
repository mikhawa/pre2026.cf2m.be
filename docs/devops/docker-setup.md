# Environnement Docker — Développement

## Services (docker-compose.yml)
| Service | Image             | Port local | Rôle |
|---------|-------------------|------------|------|
| php | custom/php8.5-fpm | — | App Symfony |
| nginx | nginx:alpine      | 8080 | Reverse proxy |
| db | mariadb:11.4      | 3306 | Base de données |
 | phpmyadmin | phpmyadmin/phpmyadmin | 8081 | Interface de gestion BDD |
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
- App : http://localhost:8080
- Mailpit : http://localhost:8025
- phpMyAdmin : http://localhost:8081 (user/pass dans .env.local)
- BDD : localhost:3306 (user/pass dans .env.local)