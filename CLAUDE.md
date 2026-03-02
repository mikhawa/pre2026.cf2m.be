# Projet : Site du Centre de Formation CF2m

**Objectif** : Plateforme de gestion des formations, inscriptions, et ressources pour les étudiants et formateurs du CF2m.

**Public cible** : Étudiants, formateurs, pouvoirs subsidiants, entreprises et administrateurs du CF2m.

**Langue du projet** : Ce projet se fait entièrement en français : code (commentaires, messages de validation, noms de commits), documentation, et échanges.

**Stack** : Symfony 7.4 LTS --webapp | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap.

**Environnements** : dev (Docker local) → préprod (GitHub CI) → prod (VPS Debian 12.13 sous Plesk).

**Repo** : https://github.com/mikhawa/pre2026.cf2m.be

## Règles d'utilisation des modèles Claude
Voir `.claude/models.md`

## Contexte rapide
- Architecture : MVC + couche Service + Repository pattern
- Auth : Symfony Security (voters custom)
- Frontend : ImportMap + Stimulus + Turbo (pas de build step)
- Déploiement : GitHub Actions → SSH VPS

## Fichiers clés à connaître
- Architecture globale : `docs/architecture/overview.md`
- Docker dev : `docs/devops/docker-setup.md`
- Schéma BDD : `docs/architecture/database-schema.md`
- Déploiement : `docs/devops/vps-preprod.md`

## Conventions de code
- Entités : `src/Entity/` — annotations Doctrine en attributs PHP 8
- Services métier : `src/Service/`
- Repositories custom : `src/Repository/`
- Nommage : PascalCase classes, snake_case BDD, camelCase JS
