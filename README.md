# pre2026.cf2m.be

Site de création du site du Centre de Formation CF2m, développé en Symfony 7.4 LTS avec PHP 8.5, MariaDB 11.4, et Docker pour l'environnement de développement local. Le projet utilise également ImportMap pour la gestion des dépendances JavaScript côté client. Des fichiers de documentation détaillent l'architecture, les conventions de code, et les processus de développement et de déploiement sont créés par Michaël J. Pitz (Mikhawa) pour assurer une maintenance facile et une évolutivité du projet avecl'IA Claude.

> Symfony 7.4 LTS | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap

#### URL du dépôt :
- https://github.com/mikhawa/pre2026.cf2m.be

#### URL du serveur de préproduction :
- https://pre2026.cf2m.be/

#### URL du serveur des raccourcis de développement :
- [Tous les raccourcis de développement](RACCOURCIS.md)
- Raccourcis de base **Git** : `gs` (status), `ga` (add), `gc` (commit), `gps` (push), `gpu` (pull)
- Raccourcis **Docker** : `dup` (up & build), `ddo` (down),
- Raccourcis **PHP** : `uphp` (shell PHP)
- Raccourcis **Symfony** : `pbc` (console), `pbcc` (cache:clear)

#### URL du serveur de développement local (Docker) :
| Service | URL | Description |
|---------|-----|-------------|
| App Symfony | http://localhost:8085 | Application principale |
| phpMyAdmin | http://localhost:8181 | Interface de gestion BDD |
| Mailpit | http://localhost:8025 | Boîte mail de test SMTP |
| BDD (MariaDB) | localhost:3307 | Connexion directe (non HTTP) |


## Création via les recommendations de Claude

- 2026-02-27

https://claude.ai/share/f3928226-c2cf-4ccf-84ea-f0c24aba6c3b

- [Raccourcis de développement](RACCOURCIS.md)

## Utilisateurs et rôles

Email : mikhawa@cf2m.be
- userName : Mikhawa
- Rôle : ROLE_SUPER_ADMIN
- Mot de passe : 123mikhawa


---

## 🚀 Démarrage rapide (développement)

```bash
# 1. Cloner le dépôt
git clone https://github.com/mikhawa/pre2026.cf2m.be.git && cd pre2026.cf2m.be

# 2. Configurer l'environnement
cp .env .env.local
# Éditer .env.local avec tes valeurs

# 3. Lancer Docker
docker compose up -d --build

# 4. Installer les dépendances et initialiser la BDD
docker compose exec php composer install
docker compose exec php bin/console doctrine:database:create
docker compose exec php bin/console doctrine:migrations:migrate

# 5. Ouvrir dans le navigateur
open http://localhost:8080
```

---

## 📚 Documentation

| Sujet | Lien |
|-------|------|
| **Point d'entrée Claude** | [`CLAUDE.md`](CLAUDE.md) |
| Architecture | [`docs/architecture/`](docs/architecture/) |
| Docker local | [`docs/devops/docker-setup.md`](docs/devops/docker-setup.md) |
| Déploiement VPS | [`docs/devops/vps-preprod.md`](docs/devops/vps-preprod.md) |
| GitHub Actions | [`docs/devops/github-actions.md`](docs/devops/github-actions.md) |
| ImportMap / JS | [`docs/architecture/importmap-strategy.md`](docs/architecture/importmap-strategy.md) |

---

## 🌐 Environnements

| Environnement | URL                     | Déploiement |
|---------------|-------------------------|-------------|
| Développement | http://localhost:8085  | Docker local |
| Préprod | https://pre2026.cf2m.be/ | Push sur `main` (GitHub Actions) |

---

## Installer les fixtures en ligne

```bashbash
php bin/console doctrine:fixtures:load
```

## 🧪 Lancer les tests

```bash
docker compose exec php bin/phpunit
docker compose exec php bin/phpunit --coverage-html coverage/
```

---

## 🤖 Utilisation avec Claude

Voir [`CLAUDE.md`](CLAUDE.md) et [`.claude/models.md`](.claude/models.md) pour les règles d'orchestration Opus/Sonnet/Haiku.
