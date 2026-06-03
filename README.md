# pre2026.cf2m.be

# TO DO
impossible de recadrer la photo de ptofil sur Android .
Améliorer les groupes et permissions. 
trouver ub design qui colle

## Description du projet

Site de création du site du **Centre de Formation CF2m**, développé en `Symfony 7.4 LTS` avec `PHP 8.5`, `MariaDB 11.4`, `Mailpit` et `Docker` pour l'**environnement de développement local**. Le projet utilise également `ImportMap` pour la gestion des dépendances `JavaScript côté client`. Des fichiers de documentation détaillent l'architecture, les conventions de code, et les processus de développement et de déploiement sont créés par **Michaël J. Pitz** ([Mikhawa](https://github.com/mikhawa)) pour assurer une maintenance facile et une évolutivité du projet avec l'**IA Claude** comme soutien.

> Symfony 7.4 LTS | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap

#### URL du dépôt :
- https://github.com/mikhawa/pre2026.cf2m.be

#### URL du serveur de préproduction :
- https://pre2026.cf2m.be/

#### URL du serveur de production :
- https://production.cf2m.be/

#### URL du serveur de développement local (Docker) :
| Service       | URL                   | Description                  |
|---------------|-----------------------|------------------------------|
| App Symfony   | http://localhost:8085 | Application principale       |
| phpMyAdmin    | http://localhost:8181 | Interface de gestion BDD     |
| Mailpit       | http://localhost:8025 | Boîte mail de test SMTP      |
| BDD (MariaDB) | localhost:3307        | Connexion directe (non HTTP) |

#### URL du serveur des raccourcis de développement :
- [Tous les raccourcis de développement](RACCOURCIS.md)
- Raccourcis de base **Git** : `gs` (status), `ga` (add), `gc` (commit), `gps` (push), `gpu` (pull)
- Raccourcis **Docker** : `dup` (up & build), `ddo` (down),
- Raccourcis **PHP** : `uphp` (shell PHP)
- Raccourcis **Symfony** : `pbc` (console), `pbcc` (cache:clear), `pbc d:f:l` (doctrine:fixtures:load), `pbc d:m:m` (doctrine:migrations:migrate), `fl` (php bin/console doctrine:fixtures:load --group=app --no-interaction), `asset` (php bin/console cache:clear --env=dev; php bin/console importmap:install; php bin/console asset-map:compile --env=dev) , `phpfix` (./vendor/bin/php-cs-fixer fix)
- Pour **améliorer la qualité du dossier** `.git`, utilise la commande `git gc` régulièrement pour compresser les objets et nettoyer les références obsolètes, ce qui peut réduire la taille du dépôt et améliorer les performances des opérations **Git**.
- Pour les **raccourcis personnalisés**, voir le fichier [RACCOURCIS.md](RACCOURCIS.md) pour la liste complète et les instructions d'installation.



## Création via les recommendations de Claude

- 2026-02-27

https://claude.ai/share/f3928226-c2cf-4ccf-84ea-f0c24aba6c3b

- [Raccourcis de développement](RACCOURCIS.md)

- 2026-03-20 : design de Clovis:

  https://cf2m-dfuse.figma.site/

- 2026-03-30 hiérarchie des rôles et permissions pour la gestion des utilisateurs et des accès à différentes parties du site, basée sur les besoins fonctionnels du projet et les meilleures pratiques de sécurité :

    [Hiérarchie](HIERARCHIE.md)

- 2026-04-01 CI/CD avec GitHub Actions pour automatiser les tests, les builds et les déploiements sur les serveurs de préproduction et de production, en utilisant des workflows définis dans `.github/workflows/` :

- 2026-04-24 : référencement de la page d'accueil en réflexion à la stratégie SEO définie avec Claude, en utilisant des balises meta, des titres optimisés, et une structure de contenu adaptée pour améliorer la visibilité sur les moteurs de recherche : https://pagespeed.web.dev/analysis/https-production-cf2m-be/nhyblij2wn?form_factor=mobile



# Passage en préproduction

- À effectuer après déploiement sur le serveur de préproduction (https://pre2026.cf2m.be/) via Git et avant de partager l'URL avec les utilisateurs finaux.
- preprod/v01 - Envoi sur cette branche pour la préproduction dorénavant.
- preprod/v02 - 2026-03-21
- preprod/v03 - 2026-03-24 (ajout de Mailjet pour l'envoi d'emails en préprod)
- preprod/v04 - 2026-03-25 (ajout des fixtures de test pour les utilisateurs et rôles)



# Passage en production
- À effectuer après validation finale en préproduction et avant de partager l'URL avec les utilisateurs finaux.
- production/ - 2026-03-24
- Création du serveur de production (https://production.cf2m.be/) avec les mêmes étapes que pour la préproduction, en veillant à utiliser les configurations d'environnement appropriées pour la production (ex. Mailjet pour l'envoi d'emails, paramètres de base de données sécurisés, etc.). - 2026-03-25
- Création d'une `ProdFixtures` pour les données de base en production (ex. compte admin initial) qui sera chargé via `php bin/console doctrine:fixtures:load --group=prod`


## Utilisateurs et rôles (fixtures de test en local et en préproduction)

Email : mikhawa@cf2m.be
- userName : Mikhawa
- Rôle : ROLE_SUPER_ADMIN
- Mot de passe : 123mikhawa

Email : thejoe@cf2m.be
- userName : TheJoe
- Rôle : ROLE_ADMIN
- Mot de passe : 123joe

Email : thelee@cf2m.be
- userName : TheLee
- Rôle : ROLE_ADMIN, ROLE_PEDAGO
- Mot de passe : 123lee

Email : therick@cf2m.be
- userName : TheRick
- Rôle : ROLE_PEDAGO, ROLE_FORMATEUR
- Mot de passe : 123rick

Email : thenoemie@cf2m.be
- userName : TheNoemie
- Rôle : ROLE_PEDAGO
- Mot de passe : 123noemie

Email : piet@cf2m.be
- userName : ThePiet
- Rôle : ROLE_ADMIN, ROLE_PEDAGO, ROLE_FORMATEUR
- Mot de passe : 123piet

Email : alex@cf2m.be
- userName : TheAlexandra
- Rôle : ROLE_FORMATEUR
- Mot de passe : 123alex

Email : greg@cf2m.be
- userName : TheGreg
- Rôle : ROLE_FORMATEUR
- Mot de passe : 123greg

Email : magib@cf2m.be
- userName : TheMagib
- Rôle : ROLE_STAGIAIRE
- Mot de passe : 123magib

Email : steve@cf2m.be
- userName : TheSteve
- Rôle : ROLE_STAGIAIRE
- Mot de passe : 123steve

Email : nabil@cf2m.be
- userName : TheNab
- Rôle : ROLE_USER
- Mot de passe : 123nabil


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

# 4.1 (optionnel) Charger les fixtures de test AppFixtures depuis l'image PHP
php bin/console doctrine:database:drop --force &&
php bin/console doctrine:database:create &&
php bin/console doctrine:migrations:migrate --no-interaction &&
php bin/console doctrine:fixtures:load --group=app --append

# 4.2 (optionnel) Lancer les tests pour vérifier que tout est en ordre
php vendor/bin/phpunit

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

| Environnement | URL                         | Déploiement                             |
|---------------|-----------------------------|-----------------------------------------|
| Développement | http://localhost:8085       | Docker local (`main`)                   |
| Préprod       | https://pre2026.cf2m.be/    | Push sur `preprod/v01` (GitHub Actions) |
| Production    | https://production.cf2m.be/ | Push sur `production` (GitHub Actions)  |

---

## Installer les fixtures en ligne

```bash
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
