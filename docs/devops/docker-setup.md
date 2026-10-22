# Environnement Docker — Développement

## Services (docker-compose.yml)
| Service | Image             | Port local | Rôle |
|---------|-------------------|------------|------|
| php | custom/php8.5-fpm | — | App Symfony |
| nginx | nginx:alpine      | 8085 | Reverse proxy |
| db | mariadb:11.4      | 3307 | Base de données |
| phpmyadmin | phpmyadmin:latest | 8181 | Interface de gestion BDD |
| mailer | mailpit           | 8025 | SMTP de test |
| node | node:24-alpine    | — | **Obsolète** (îlots React retirés) : à supprimer de `docker-compose.yml` |
| frontend | node:24-alpine | 5173 | Serveur de dev Vite de la SPA React (`frontend/`), relaie `/api`, `/admin`… vers nginx |

## Commandes quotidiennes
```bash
docker compose up -d
docker compose exec php bin/console [commande]
docker compose exec php composer install
docker compose exec php bin/console doctrine:migrations:migrate
docker compose logs -f frontend      # suivre le serveur de dev Vite (SPA)
docker compose exec frontend npm install <paquet>   # ajouter une dépendance à la SPA
docker compose exec frontend npm run build     # compiler la SPA dans frontend/dist (non versionné)
```

## Variables d'environnement dev
Fichier `.env.local` (non commité) — voir `.env` pour le template

## Accès
- App (Twig, actuelle) : http://localhost:8085
- SPA React (en construction) : http://localhost:5173
- Mailpit : http://localhost:8025
- phpMyAdmin : http://localhost:8181 (user/pass dans .env.local)
- BDD : localhost:3307 (user/pass dans .env.local)

## Intégration MCP (PHPStorm)
Serveur MCP `phpstorm` déclaré dans `.mcp.json` (racine du projet, commité) et activé dans `.claude/settings.local.json` (`enabledMcpjsonServers`, non commité — spécifique au poste).
- Type : HTTP, exposé localement par le plugin PHPStorm (`http://127.0.0.1:64442/stream`)
- Permet à Claude Code d'interroger l'IDE (navigation, diagnostics) depuis le terminal
- Nécessite PHPStorm ouvert sur le projet avec le plugin MCP actif