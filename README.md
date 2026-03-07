# pre2026.cf2m.be

## Création via les recommendations de Claude

- 2026-02-27

https://claude.ai/share/f3928226-c2cf-4ccf-84ea-f0c24aba6c3b

> Symfony 7.4 LTS | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap

- 2026-06-10 : mise à jour du README avec les liens vers la documentation d'architecture, devops, et les règles d'utilisation des modèles Claude.

## URL utiles en développement

Lancer `docker compose up -d --build` puis accéder aux services via les URLs suivantes :

| Service | URL | Description |
|---------|-----|-------------|
| App Symfony | http://localhost:8085 | Application principale |
| phpMyAdmin | http://localhost:8181 | Interface de gestion BDD |
| Mailpit | http://localhost:8025 | Boîte mail de test SMTP |
| BDD (MariaDB) | localhost:3307 | Connexion directe (non HTTP) |

Pour PHP, utiliser `docker compose exec php sh` pour ouvrir un shell dans le conteneur et exécuter les commandes Symfony habituelles (`php bin/console`, `composer`, etc.).

Ne pas oublier de fermer les conteneurs avec `docker compose down` après la session de développement.

## Utilisateurs et rôles

Email : mikhawa@cf2m.be
- userName : Mikhawa
- Rôle : ROLE_SUPER_ADMIN
- Mot de passe : 123mikhawa

En ligne il faudra installer mailjet :

https://www.mailjet.com/


## 🛠️ Raccourcis et commandes utiles

À mettre dans le `.bashrc` ou `.zshrc` pour gagner du temps lors du développement.

```bash
nano ~/.bashrc
```

### ----------------------
### Git Commands
### ----------------------
```bash
alias gs='git status'
alias ga='git add .'
alias gc='git commit'
alias gps='git push'
alias gpu='git pull'
```
---

### ----------------------
### Docker Commands
### ----------------------
```bash
alias uphp='docker compose exec php sh'
alias dphp='docker compose exec -it php bash'
alias dup='docker compose up -d --build'
alias ddo='docker compose down'
alias phpfix='./vendor/bin/php-cs-fixer fix'
alias asset='php bin/console asset-map:compile'
```
---

### ----------------------
### Symfony Commands
### ----------------------
```bash
alias pbc='php bin/console'
alias ddc='php bin/console doctrine:database:create'
alias sssd='symfony serve -d'
alias sss='symfony server:stop'
alias dfl='php bin/console doctrine:fixture:load'
alias test='vendor/bin/phpunit --testdox'
alias csfix='./vendor/bin/php-cs-fixer fix'
```
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
