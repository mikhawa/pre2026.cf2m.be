# pre2026.cf2m.be

## Création via les recommendations de Claude

- 2026-02-27

https://claude.ai/share/f3928226-c2cf-4ccf-84ea-f0c24aba6c3b

> Symfony 7.4 LTS | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap

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
| Développement | http://localhost:8080   | Docker local |
| Préprod | https://preprod.cf2m.be | Push sur `main` (GitHub Actions) |

---

## 🧪 Lancer les tests

```bash
docker compose exec php bin/phpunit
docker compose exec php bin/phpunit --coverage-html coverage/
```

---

## 🤖 Utilisation avec Claude

Voir [`CLAUDE.md`](CLAUDE.md) et [`.claude/models.md`](.claude/models.md) pour les règles d'orchestration Opus/Sonnet/Haiku.