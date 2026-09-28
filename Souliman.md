# Passation du projet CF2m — Document d'accueil pour Soliman

> Rédigé le 2026-09-28 par Michaël J. Pitz (Mikhawa), avec l'aide de Claude.
> Objectif : te donner une vue complète de **ce qui existe déjà** avant que tu attaques
> le nouveau design et les nouveaux outils. Ce document est un point d'entrée : il renvoie
> vers la documentation détaillée existante (`docs/`, `.claude/`, `HIERARCHIE.md`…).

---

## Sommaire

1. [Le projet en deux minutes](#1-le-projet-en-deux-minutes)
2. [Démarrer en local](#2-démarrer-en-local)
3. [Stack technique](#3-stack-technique)
4. [Arborescence du dépôt](#4-arborescence-du-dépôt)
5. [Modèle de données](#5-modèle-de-données)
6. [Rôles, sécurité et permissions](#6-rôles-sécurité-et-permissions)
7. [Site public : routes et pages](#7-site-public--routes-et-pages)
8. [Back-office EasyAdmin](#8-back-office-easyadmin)
9. [Workflow de révisions (historique)](#9-workflow-de-révisions-historique)
10. [Emails](#10-emails)
11. [Frontend actuel (ce que tu vas redessiner)](#11-frontend-actuel-ce-que-tu-vas-redessiner)
12. [Tests et qualité](#12-tests-et-qualité)
13. [Branches, CI/CD et déploiement](#13-branches-cicd-et-déploiement)
14. [Conventions de travail](#14-conventions-de-travail)
15. [Pièges connus](#15-pièges-connus)
16. [Travail en cours, TODO et dette connue](#16-travail-en-cours-todo-et-dette-connue)
17. [Recommandations pour la refonte du design](#17-recommandations-pour-la-refonte-du-design)
18. [Index de la documentation existante](#18-index-de-la-documentation-existante)

---

## 1. Le projet en deux minutes

Site du **Centre de Formation CF2m** (formations professionnelles gratuites aux métiers du numérique, Belgique).

Ce que fait le site aujourd'hui :

- **Vitrine publique** : accueil (hero, liste des formations, partenaires), fiche de chaque formation,
  réalisations des stagiaires (« Works »), pages de contenu (« Nos activités »), formulaire de contact.
- **Préinscription** à une formation lorsqu'elle est en statut `recruiting`.
- **Comptes utilisateurs** : inscription avec confirmation par email, connexion, mot de passe oublié,
  profil (avatar recadré, biographie, liens externes), **double authentification par email** pour les rôles sensibles.
- **Back-office EasyAdmin** (`/admin`) : gestion des formations, works, pages, utilisateurs,
  inscriptions, messages de contact, commentaires, notes, partenaires, stagiaires par formation.
- **Workflow de révisions** : chaque modification de Formation / Page / Works crée une version
  historisée ; certaines modifications doivent être validées par un admin (approuver / rejeter / restaurer).
- **Permissions fines** : un formateur responsable d'une formation a des droits d'admin sur *sa* formation.

Public cible : stagiaires, formateurs, pouvoirs subsidiants, entreprises, administrateurs.

**Langue du projet : tout en français** (code commenté en français, commits en français, docs en français).
Les noms de classes/méthodes restent en anglais ou français selon l'existant — suis l'existant.

---

## 2. Démarrer en local

Prérequis : Docker + Docker Compose, Git. (Le développement se fait sous WSL2/Linux.)

```bash
git clone https://github.com/mikhawa/pre2026.cf2m.be.git && cd pre2026.cf2m.be
docker compose up -d --build
docker compose exec php composer install
docker compose exec php php bin/console doctrine:database:create --if-not-exists
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load --group=app --no-interaction
docker compose exec php php bin/console importmap:install
```

| Service | URL / port | Notes |
|---|---|---|
| Application | http://localhost:8085 | Nginx → PHP-FPM 8.5 |
| Mailpit (emails de dev) | http://localhost:8025 | Tous les emails de dev arrivent ici (y compris les codes 2FA) |
| phpMyAdmin | http://localhost:8181 | |
| MariaDB | localhost:3307 | user `pre_cf2m_user` / base `pre_cf2m_db` (voir `docker-compose.yml`) |

Conteneurs : `pre_cf2m_php`, `pre_cf2m_nginx`, `pre_cf2m_db`, `pre_cf2m_phpmyadmin`, `pre_cf2m_mailpit`.
Dockerfile PHP : `docker/php/Dockerfile`, conf Nginx : `docker/nginx/default.conf`.

**Comptes de test** : la liste complète (email, rôle, mot de passe) se trouve dans `README.md`,
section « Utilisateurs et rôles ». Ils sont créés par `src/DataFixtures/AppFixtures.php` (groupe `app`).
Il existe aussi un groupe `prod` (`ProdFixtures`, compte admin initial). Pour te connecter en tant
qu'admin, le code 2FA est à récupérer dans Mailpit.

**Raccourcis shell** : voir `RACCOURCIS.md` (`pbc` = console, `fl` = recharger les fixtures,
`asset` = cache:clear + importmap:install + asset-map:compile, `phpfix` = php-cs-fixer…).

**Fichier `.env.local`** : non versionné, pour tes surcharges locales. Variables utilisées :
`APP_ENV`, `APP_SECRET`, `DATABASE_URL`, `MAILER_DSN`, `MAIL_ADMIN` (destinataire admin),
`MAIL_FORM` (expéditeur), `TURNSTILE_SITE_KEY` / `TURNSTILE_SECRET_KEY` (Cloudflare Turnstile —
des clés de **test** toujours valides sont dans `.env`), `MESSENGER_TRANSPORT_DSN`, `DEFAULT_URI`.

---

## 3. Stack technique

| Couche | Technologie |
|---|---|
| Framework | Symfony **7.4 LTS** (`--webapp`) |
| Langage | PHP **8.5**, `declare(strict_types=1)` partout |
| BDD | MariaDB **11.4** (utf8mb4) — Doctrine ORM 3 + Migrations |
| Back-office | **EasyAdmin** (`easycorp/easyadmin-bundle` ^5.6 dans `composer.json` ; la doc parle encore d'« EasyAdmin 4 », les conventions restent valables) |
| Uploads | `vich/uploader-bundle` (avatars, logos formation, logos partenaires) |
| Éditeur riche | **SunEditor** 2.47 (back-office uniquement) |
| Frontend | **AssetMapper + ImportMap** (aucun build Node/Webpack/Vite), **Stimulus** 3, **Turbo** 7, **Bootstrap 5.3.8** |
| Recadrage d'image | `cropperjs` 2.1 (avatar) |
| Emails | Symfony Mailer — Mailpit en dev, **Mailjet** en préprod/prod (`symfony/mailjet-mailer`, ne pas retirer) |
| Anti-bot | Cloudflare **Turnstile** (`App\Service\TurnstileVerifier`) + honeypot sur le contact |
| Analytics | Matomo (partial `templates/_matomo.html.twig`, chargé seulement en `prod`) |
| Tests | PHPUnit 13, Foundry 2 (factories), Doctrine Fixtures |
| Qualité | php-cs-fixer |
| CI/CD | GitHub Actions → SSH sur VPS Debian 12 / Plesk |

---

## 4. Arborescence du dépôt

```
src/
├── Command/               # Commandes console (ex. MigrateRevisionsCommand — migration historique ponctuelle)
├── Controller/            # Contrôleurs publics (Home, Formation, Works, Page, Contact, Inscription,
│   │                      #   Registration, Security, Profile, TwoFactor…)
│   └── Admin/             # EasyAdmin : DashboardController + un CrudController par entité
│                          #   + contrôleurs AJAX (Inscription, ContactMessage, Comment)
│                          #   + RevisionsPendantesController, HistoryPreviewController, MediaUploadController
├── DataFixtures/          # AppFixtures (groupe app), ProdFixtures (groupe prod)
├── Entity/                # Entités Doctrine (attributs PHP 8)
│   └── Trait/             # RevisionWorkflowTrait (commun aux tables d'historique)
├── EventSubscriber/       # TwoFactorLoginSubscriber, TwoFactorKernelSubscriber,
│                          #   PartenaireLogoResizeSubscriber, subscriber Turnstile…
├── Form/                  # FormTypes (RegistrationType, ContactType, InscriptionType, ProfileEditType…)
├── Repository/            # Repositories custom (requêtes métier : findAllPublished, countPending…)
├── Security/
│   ├── UserChecker.php    # Bloque la connexion des comptes bannis / non activés
│   └── Voter/             # FormationVoter, WorksVoter, ContentManagerVoter, FormationStagiaireVoter
├── Service/               # RevisionService, StagiaireService, TwoFactorEmailService, TurnstileVerifier…
└── Twig/                  # Extensions Twig : nav_formations(), nav_pages(), pending_revisions_count()

templates/                 # Twig (voir §11)
assets/
├── app.js                 # Point d'entrée public (Bootstrap + app.css + Stimulus + CSRF)
├── admin.js               # Point d'entrée back-office (SunEditor, slug auto, modules AJAX, admin.css)
├── controllers/           # Contrôleurs Stimulus
├── styles/app.css         # TOUT le style du site public (gros fichier, dark + light)
├── styles/admin.css       # Style du back-office
└── *.js                   # Modules admin : inscription_treat, contact_read, comment_approve, works_etudiants_filter
config/                    # Configuration Symfony (security.yaml, vich_uploader.yaml, csrf.yaml…)
migrations/                # Migrations Doctrine
public/                    # Racine web : images/, uploads/ (avatars, logos, editor)
tests/                     # PHPUnit
docs/                      # Documentation technique (architecture, devops)
documentations-dev/        # Journal des changements (un fichier par changement, numéroté)
.claude/                   # Mémoire et contexte pour Claude Code (MEMORY.md, CONTEXT.md, models.md, plans/)
.claude-tasks/             # Traçabilité des tâches faites avec Claude (haiku/ sonnet/ opus/)
.github/workflows/         # symfony.yml, deploy-preprod.yml, deploy-prod.yml
datas/                     # Fichiers de travail (maquettes reçues, scripts ponctuels)
docker/                    # Dockerfile PHP, conf Nginx
```

> ⚠️ `.claude/CONTEXT.md` décrit une liste de services « cibles » (AuthService, TokenService,
> CacheService, AuditService…) dont **beaucoup n'existent pas** dans le code : c'était la vision
> initiale. Fie-toi au contenu réel de `src/Service/`. Une partie de la logique (reset password,
> inscription, contact) est encore directement dans les contrôleurs.

---

## 5. Modèle de données

Référence complète (champs, types, diagramme Mermaid) : **`docs/architecture/database-schema.md`**.

| Entité | Rôle | Points clés |
|---|---|---|
| `User` | Compte | email unique, `userName` unique, `roles` JSON, `status` (0 inactif / 1 actif / 2 banni), avatar Vich, bio, 3 liens externes, champs 2FA et tokens (activation, reset) |
| `Formation` | Formation proposée | `slug` unique, `status` : `draft` / `published` / `recruiting` / `archived`, description riche, `descriptionCourte` (SEO), logo Vich, **`colorPrimary` / `colorSecondary`** (couleur propre à chaque formation, utilisée dans le design), responsables (ManyToMany User) |
| `FormationStagiaire` | Pivot stagiaire ↔ formation | unique (formation, user) ; son existence synchronise `ROLE_STAGIAIRE` sur l'utilisateur (`StagiaireService`) |
| `Works` | Réalisation de stagiaire | rattaché à une formation, auteurs (ManyToMany User), statut draft/published/archived |
| `Comment` | Commentaire sur un Works | modération obligatoire (`isApproved`) |
| `Rating` | Note 1–5 | sur Works et Comment |
| `Inscription` | Préinscription à une formation | `treat` / `treatAt` / `treatBy` (suivi du traitement) |
| `ContactMessage` | Message du formulaire de contact | `isRead` / `readBy` |
| `Page` | Page de contenu (« Nos activités », mentions…) | slug, contenu riche, statut, éditeurs |
| `Partenaire` | Partenaire affiché sur l'accueil | logo redimensionné auto (400×300 max), `isActive` |
| `FormationHistory`, `PageHistory`, `WorksHistory` | Versions historisées | voir §9 |

Uploads (VichUploader, `config/packages/vich_uploader.yaml`) :
`public/uploads/avatars`, `public/uploads/formation-logos`, `public/uploads/partenaire-logos`,
et `public/uploads/editor` pour les images insérées via SunEditor (`MediaUploadController`).

Conventions BDD : `snake_case`, `id` unsigned, FK suffixées `_id`, `createdAt` via `#[ORM\PrePersist]`.

---

## 6. Rôles, sécurité et permissions

Références : `HIERARCHIE.md`, `docs/architecture/permissions-fines-formations.md`, `config/packages/security.yaml`.

### Hiérarchie

```
ROLE_SUPER_ADMIN → ROLE_ADMIN → ROLE_FORMATEUR → ROLE_STAGIAIRE → ROLE_USER
ROLE_PEDAGO      → ROLE_FORMATEUR  (parallèle à ROLE_ADMIN, n'en hérite PAS)
```

- **USER** : compte simple (profil).
- **STAGIAIRE** : accès au back-office limité à *ses* works (ses modifications passent en attente de validation).
- **FORMATEUR** : responsable d'une ou plusieurs formations → droits d'admin **sur celles-ci uniquement**.
- **PEDAGO** : crée/modifie les formations, gère pages, inscriptions, utilisateurs, contacts ; works en lecture seule ; ne reçoit pas les mails de révision.
- **ADMIN** : tout sauf suppression et rôles sensibles. **SUPER_ADMIN** : tout, y compris suppressions.

### Contrôle d'accès

- `access_control` : `/admin` → `ROLE_STAGIAIRE`, `/profil` → `ROLE_USER`, `/double-authentification` → authentifié.
- **Voters** (`src/Security/Voter/`) : `FORMATION_*` (create, edit_autoapprove, approve, reject, restore),
  `WORKS_*`, `CONTENT_MANAGER` (admin ou pédago), `FORMATION_MANAGE_STAGIAIRES`.
- Login : `form_login` sur `/connexion`, CSRF activé, **throttling 5 tentatives / 5 min**, `UserChecker` refuse bannis/non activés.
- **2FA par email** pour `SUPER_ADMIN`, `ADMIN`, `PEDAGO` : code 6 chiffres, TTL 15 min,
  `TwoFactorLoginSubscriber` + `TwoFactorKernelSubscriber` (bloque tout le site tant que non validé).
- **CSRF stateless** (`SameOriginCsrfTokenManager`, `config/packages/csrf.yaml`) : le fichier
  `assets/controllers/csrf_protection_controller.js` **doit rester importé dans `assets/app.js`**,
  sinon tous les formulaires répondent « Jeton CSRF invalide ».
- **Turnstile** (Cloudflare) sur contact, préinscription, mot de passe oublié (et login).

---

## 7. Site public : routes et pages

| Route (name) | URL | Contrôleur | Template |
|---|---|---|---|
| `app_home` | `/` | `HomeController` | `home/index.html.twig` |
| `app_formation_show` | `/formation/{slug}` | `FormationController` (published ou recruiting seulement) | `formation/show.html.twig` |
| `app_works_show` | `/formation/{formationSlug}/works/{slug}` | `WorksController` | `works/show.html.twig` |
| `app_page_show` | `/activites/{slug}` | `PageController` | `page/show.html.twig` |
| `app_contact` / `app_contact_success` | `/contact` | `ContactController` | `contact/index.html.twig`, `success.html.twig` |
| `app_inscription_create` | `POST /preinscription/{formationSlug}` | `InscriptionController` | formulaire intégré à `formation/show` |
| `app_register` | `/inscription` | `RegistrationController` | `registration/register.html.twig` |
| `app_verify_email` | `/inscription/verification/{token}` | idem — active le compte et **envoie un mot de passe généré** par email | — |
| `app_login` / `app_logout` | `/connexion`, `/deconnexion` | `SecurityController` | `security/login.html.twig` |
| `app_forgot_password` | `/mot-de-passe-oublie` | idem | `security/forgot_password.html.twig` |
| `app_reset_password` | `/reinitialisation-mot-de-passe/{token}` (token valable 1 h) | idem | `security/reset_password.html.twig` |
| `app_two_factor` (+ `_resend`) | `/double-authentification` | `TwoFactorController` | `security/two_factor.html.twig` |
| `app_profile` | `/profil` | `ProfileController` | `profil/index.html.twig` |
| `app_profile_edit` | `/profil/modifier` | idem | `profil/edit.html.twig` |
| `app_profile_users` | `/profil/utilisateurs` (FORMATEUR+) | idem | `profil/utilisateurs.html.twig` |
| `app_profile_request_reset` | `POST /profil/demande-reinitialisation` | idem | — |
| `app_public_profile` | `/utilisateur/{id}` | profil public | `profil/public.html.twig` |

> Pour avoir la liste exacte et à jour : `docker compose exec php php bin/console debug:router`.

**Navigation** (dans `base.html.twig`) : Accueil · Nos formations (menu déroulant alimenté par
`nav_formations()`, badge « Recrutement ») · Nous contacter · Nos activités (menu alimenté par
`nav_pages()`) · lien externe CF2d · bouton thème · menu utilisateur (profil, administration avec
badge de révisions en attente, déconnexion).

---

## 8. Back-office EasyAdmin

Référence : **`docs/architecture/easyadmin.md`**. Point d'entrée : `src/Controller/Admin/DashboardController.php` (route `/admin`).

Menu (avec badges rouges de compteurs) :

- **Contenu** : Formations (badge révisions en attente), Works, Pages
- **Utilisateurs** : Utilisateurs, Inscriptions (badge non traitées)
- **Interactions** : Commentaires (badge non approuvés), Notes, Révisions en attente
- **Communication** : Messages de contact (badge non lus), Partenaires

Particularités :

- SunEditor s'initialise sur les `textarea.ea-suneditor-field` (dans `assets/admin.js`), upload d'images via `MediaUploadController`.
- Slug généré automatiquement depuis le titre en JS (sans écraser une saisie manuelle).
- Mises à jour AJAX sans rechargement : traitement d'inscription, lecture de message, approbation de commentaire.
- Action « Stagiaires » sur une formation : ajout/retrait de stagiaires (`FormationStagiaire`).
- Action « Historique » sur Formation/Page/Works : timeline des versions, prévisualisation, approuver/rejeter/restaurer.
- Le CSS admin est importé **depuis `admin.js`** (`import './styles/admin.css'`), pas via `addHtmlContentToHead()`.
- Templates admin custom : `templates/admin/` et overrides dans `templates/bundles/EasyAdminBundle/`.

---

## 9. Workflow de révisions (historique)

Références : `docs/architecture/decision-historique-revisions.md`, `permissions-fines-formations.md`.

- Chaque enregistrement d'une Formation / Page / Works crée un **snapshot** dans `formation_history`,
  `page_history` ou `works_history` (versions numérotées, responsables/auteurs figés).
- Statut de révision : `0 PENDING`, `1 APPROVED`, `2 REJECTED`, `3 AUTO_APPROVED`.
- Qui passe en `PENDING` : un **stagiaire** qui modifie son works (notifie les responsables de la
  formation), un **formateur** qui modifie une page (notifie les admins). Les autres sont auto-approuvés.
- Service central : `App\Service\RevisionService` (création, application, notifications email).
- Page centralisée « Révisions en attente » : `RevisionsPendantesController` (ROLE_ADMIN).
- L'ancienne entité `Revision` n'est plus qu'un DTO transitoire (la table a été supprimée) — ne pas la réutiliser.

---

## 10. Emails

Templates : `templates/emails/` (confirmation d'inscription, bienvenue avec identifiants,
reset password, code 2FA, notification de préinscription, de contact, de révision…).

- Expéditeur : `MAIL_FORM`. Destinataire admin : `MAIL_ADMIN` + utilisateurs ciblés via
  `UserRepository::findInscriptionRecipients()` / `findContactRecipients()`.
- Dev : Mailpit. Préprod/Prod : Mailjet (`MAILER_DSN=mailjet+api://…` dans `.env.local` du serveur).
- Si le serveur de préprod tourne en `APP_ENV=dev`, **tous** les emails sont redirigés vers une
  adresse unique (`config/packages/dev/mailer.yaml`).
- Si tu changes la charte graphique, pense aussi aux **templates d'emails** (styles inline).

---

## 11. Frontend actuel (ce que tu vas redessiner)

Références : `docs/architecture/frontend.md`, `docs/architecture/seo-performances.md`, `docs/architecture/analytics.md`.

### Principes

- **Pas de build step** : les JS/CSS sont servis par AssetMapper. Pour ajouter une lib :
  `php bin/console importmap:require <paquet>` (elle est ajoutée dans `importmap.php`).
- Deux points d'entrée : `app` (site public) et `admin` (back-office).
- **Bootstrap 5** est utilisé partout dans les templates (grille, navbar, dropdowns, formulaires,
  alertes). Les classes maison sont préfixées **`cf2m-`** (`.cf2m-navbar`, `.cf2m-card`,
  `.cf2m-hero-*`, `.cf2m-login-*`, `.cf2m-footer`, `.cf2m-dropdown`…).
- Tout le style public est dans **`assets/styles/app.css`** (fichier volumineux : tokens `:root`,
  composants, puis un grand bloc `[data-theme="light"]`).

### Templates

```
templates/
├── base.html.twig            # layout : <head>, navbar, footer, anti-flash thème, Matomo (prod)
├── _matomo.html.twig
├── home/index.html.twig      # hero + stats (100 % gratuit, 80 % pratique, 1600 h), formations, partenaires
├── formation/show.html.twig  # fiche formation + works publiés + formulaire de préinscription
├── works/show.html.twig      # réalisation + partage réseaux sociaux + meta Open Graph
├── page/show.html.twig
├── contact/                  # index + success
├── security/                 # login, forgot_password, reset_password, two_factor
├── registration/register.html.twig
├── profil/                   # index, edit (recadrage avatar), public, utilisateurs
├── admin/                    # dashboard, révisions, historiques, stagiaires
├── bundles/EasyAdminBundle/  # overrides EasyAdmin
└── emails/
```

Blocs surchargeables de `base.html.twig` : `title`, `meta_description`, `meta_robots`, `meta_og`,
`stylesheets`, `javascripts` / `importmap`, `body_class`, `body`.
Le `<body>` porte `data-page="home"` ou `"inner"` (utilisé par le CSS pour différencier l'accueil).

### Thème sombre / clair

- Sombre par défaut ; bascule stockée dans `localStorage` (clé `cf2m-theme`), appliquée sur
  `<html data-theme="…">` par un **script anti-flash inline** dans le `<head>`.
- `assets/controllers/theme_controller.js` ; deux boutons (desktop et mobile) synchronisés par CSS
  (`.cf2m-theme-sun` / `.cf2m-theme-moon`).
- Logo : deux `<img>` (blanc pour dark, bleu pour light) basculées en CSS.
- Palette actuelle (après le restyle « raffiné » d'août 2026) : fond sombre `#050e18`, navy `#0d1e35`,
  accent cyan `#3cc8e6` (dark) / `#0072a3` (light), or `#f4c430`. Polices Google : **Outfit** (titres)
  et **DM Sans** (texte). ⚠️ Le tableau de couleurs de `docs/architecture/frontend.md` date d'avant ce restyle.
- Fond light : `#f5f7f9` + halos `radial-gradient` bleutés ; accueil/hero : `#c4dff0`.

### Contrôleurs Stimulus (`assets/controllers/`)

| Contrôleur | Rôle |
|---|---|
| `theme_controller.js` | bascule dark/light |
| `avatar_crop_controller.js` | recadrage de l'avatar (cropperjs) avant upload |
| `csrf_protection_controller.js` | CSRF stateless — **indispensable** |
| `suneditor_controller.js` | SunEditor (back-office) |
| `hello_controller.js` | scaffold Symfony, inutilisé |

### Historique design

- Un premier design (Figma de Clovis) : https://cf2m-dfuse.figma.site/
- En août 2026, un mockup « Site raffiné white/dark » (dans `datas/`) a été utilisé **comme simple
  référence visuelle** : on a gardé Bootstrap et toutes les fonctionnalités, et porté palette,
  typographie et rayons (boutons « pill ») dans le CSS existant. Plan : `.claude/plans/foamy-sniffing-petal.md`.
- Les images du hero (`public/images/hero-bg.jpg`, `hero-portrait.jpg`) sont actuellement des
  **photos stock**, pas de vraies photos du CF2m → à remplacer. Les `.webp` associés sont orphelins.

---

## 12. Tests et qualité

```bash
docker compose exec php php bin/phpunit
docker compose exec php ./vendor/bin/php-cs-fixer fix
docker compose exec php php bin/console lint:twig templates/
```

- Tests dans `tests/` (fonctionnels + unitaires), factories **Foundry 2** (`createOne()`/`createMany()`
  renvoient directement les entités ; pour les booléens `isActive`, utiliser la clé `active`, etc.).
- En CI, les tests tournent sur **SQLite** (pas MariaDB) — évite le SQL spécifique MariaDB dans les requêtes.
- Toute PR vers `main` déclenche `symfony.yml` (tests).

---

## 13. Branches, CI/CD et déploiement

Références : `docs/devops/github-actions.md`, `docs/devops/vps-preprod.md`.

| Branche | Workflow | Effet |
|---|---|---|
| `main` | `symfony.yml` | tests uniquement |
| `preprod/*` (actuellement `preprod/v01`) | `deploy-preprod.yml` | tests + déploiement sur https://pre2026.cf2m.be/ |
| `production` | `deploy-prod.yml` | tests + déploiement sur https://production.cf2m.be/ |

- Branches de travail : `feature/NN-description` ou `fix/NN-description`, puis PR vers `main`.
- Déploiement = SSH (`appleboy/ssh-action`) : `git pull`, `composer install`, migrations,
  cache, `importmap:install` + `asset-map:compile`, permissions sur `public/uploads` et `var/`.
- ⚠️ **La préprod recharge les fixtures (`--group=app`) à chaque déploiement** : les données de
  préprod sont **écrasées** à chaque push sur `preprod/*`. Ne jamais y stocker de vraies données.
- La prod tourne en `--env=prod`, `composer install --no-dev`, sans fixtures.
- Secrets GitHub : `PROD_SSH_PRIVATE_KEY`, `PROD_VPS_HOST`, `PROD_VPS_USER`, `PROD_VPS_PATH`.
- Serveur : Debian 12.13, Plesk, Nginx, PHP 8.5-FPM, MariaDB 11.4. `.env.local` est géré à la main sur le serveur.
- Toute modification de schéma passe par une **migration Doctrine** (`make:migration`), jamais à la main.
  `doctrine_migrations.yaml` a `transactional: false` (DDL implicites de MariaDB).

---

## 14. Conventions de travail

**Code PHP**
- `declare(strict_types=1)` dans chaque fichier ; attributs PHP 8 pour Doctrine, routes, validation.
- Contrôleurs fins, logique métier dans `src/Service/`, requêtes dans `src/Repository/`.
- Permissions via voters, pas de tests de rôle éparpillés dans les templates quand un voter existe.
- Entités : `User.php` sert de modèle (setters qui retournent `static`, `__toString()`, `orphanRemoval: true` sur les OneToMany, PHPDoc des collections).
- Nommage : PascalCase (classes), camelCase (JS/PHP), snake_case (BDD).

**Documentation / traçabilité** (c'est une habitude du projet, garde-la si possible)
- `documentations-dev/NNN-YYYY-MM-DD_HH-MM_Description.md` : une fiche par changement (date, fichiers, résumé, raison).
- `.claude-tasks/<modèle>/NNN-YYYY-MM-DD_description.md` : une fiche par tâche réalisée avec Claude Code.
- Mettre à jour `docs/` quand l'architecture change.

**Claude Code** : le projet est configuré pour être travaillé avec Claude Code (`CLAUDE.md`,
`.claude/MEMORY.md`, `.claude/models.md`, agent `design-review` dans `.claude/agents/` qui relit
templates/CSS/Stimulus pour la cohérence visuelle et l'accessibilité — utile pour ta refonte).
Après clone, crée le lien symbolique de mémoire décrit dans `CLAUDE.md`.

---

## 15. Pièges connus

1. **Cache AssetMapper en dev** : après une modif CSS/JS, si le changement ne s'affiche pas,
   `php bin/console asset-map:compile --env=dev` puis vérifie que le hash du fichier servi a changé
   (supprimer `public/assets/` ne suffit pas toujours). Un `public/assets/` compilé qui traîne
   masque tes modifications.
2. **Spécificité CSS en mode clair** : des règles génériques `[data-theme="light"] a` ou
   `[data-theme="light"] .btn-…` écrasent les classes `.cf2m-*`. Plusieurs bugs de contraste en
   sont venus (bouton du hero, en-têtes de cartes, footer). Teste **chaque page dans les deux thèmes**.
3. **Composants restés sombres en mode clair** (ex. `.cf2m-card .card-header`) : un token recoloré
   pour fond blanc devient illisible dedans. Vérifie toujours le contexte.
4. **Navbar** : en dark, fond `rgba(6,14,26,.80)` identique partout ; en light, navbar et footer
   sont blancs (logo bleu). Pas de `backdrop-filter` sur fond blanc.
5. **VichUploader + session** : après upload d'avatar, remettre `setAvatarFile(null)` avant la fin
   de la requête (sinon « Serialization of File is not allowed »).
6. **EasyAdmin et JS** : les liens du menu sont en `/admin/<entite>` ; cibler les cellules via
   `td[data-column="…"]` / `tr[data-id]` ; utiliser la délégation d'événements sur `document` pour
   survivre aux navigations Turbo.
7. **Turbo** : tout JS qui s'initialise au `DOMContentLoaded` doit aussi écouter `turbo:load`
   (ou, mieux, être un contrôleur Stimulus).
8. **Recadrage d'avatar impossible sur Android** (noté dans le README, non résolu).
9. Routes en français : `/connexion`, pas `/login`.

---

## 16. Travail en cours, TODO et dette connue

- **Sécurité — à traiter en priorité** : la route publique `app_public_profile` (`/utilisateur/{id}`)
  expose des données de profil **sans authentification** (ids séquentiels énumérables). Identifié, non corrigé.
- Mot de passe envoyé **en clair par email** à l'activation du compte (`RegistrationController::verifyEmail`) —
  à remplacer idéalement par un lien « choisir mon mot de passe ».
- Export CSV des inscriptions et messages de contact.
- Transformation automatique d'une `Inscription` acceptée en `FormationStagiaire` (aujourd'hui manuel).
- Autocomplétion AJAX pour l'ajout de stagiaires (aujourd'hui un `<select>` simple).
- « Améliorer les groupes et permissions » (README).
- Optimiser `logo-cf2m-blanc.svg` (136 Ko → < 10 Ko avec SVGO) ; héberger les Google Fonts en local.
- Vraies photos du CF2m pour le hero.
- `hello_controller.js` et la commande `MigrateRevisionsCommand` / DTO `Revision` sont des restes à nettoyer.
- La branche actuelle `feature/31-passage-a-soliman` contient une mise à jour de `composer.json`/`composer.lock` non commitée au moment de la rédaction.

---

## 17. Recommandations pour la refonte du design

1. **Ne pas casser le fonctionnel** : chaque formulaire public dépend de Turnstile, du CSRF stateless
   et des FormTypes existants ; la fiche formation contient le formulaire de préinscription (si `recruiting`).
   Garde les noms de champs et les routes.
2. **Données réelles uniquement** : l'entité `Formation` n'a ni prix, ni durée, ni modules. Si le
   nouveau design en a besoin, il faut ajouter les champs (entité + migration + CrudController + fixtures).
3. **Couleur par formation** (`colorPrimary` / `colorSecondary`) : fonctionnalité voulue du back-office,
   à intégrer dans le nouveau design plutôt qu'à supprimer.
4. **Deux thèmes** : le toggle dark/light est attendu. Si tu repars de zéro côté CSS, pars d'un
   système de **tokens CSS** (couleurs, rayons, espacements) redéfinis sous `[data-theme="light"]`
   plutôt que de surcharger composant par composant (c'est la source des bugs actuels).
5. Si tu abandonnes Bootstrap, attention : navbar/dropdowns/collapse s'appuient sur le JS Bootstrap
   (`data-bs-toggle`), et beaucoup de templates utilisent sa grille. Le back-office EasyAdmin a son propre style (`admin.css`) — indépendant.
6. Pages à couvrir (≈ 20) : accueil, formation, works, activités, contact (+ succès), login,
   inscription, mot de passe oublié, reset, 2FA, profil (index, édition, public, membres),
   pages d'erreur, emails.
7. Vérifie visuellement (captures) chaque page dans les deux thèmes et en mobile, et garde l'accessibilité
   (contrastes, `aria-label`, focus visibles). L'agent `design-review` de Claude Code peut t'aider.
8. SEO : conserve les blocs `meta_description`, `meta_og`, le lazy loading et les images WebP (voir `seo-performances.md`).

---

## 18. Index de la documentation existante

| Sujet | Fichier |
|---|---|
| Règles du projet (Claude Code) | `CLAUDE.md` |
| Démarrage, comptes de test, environnements | `README.md` |
| Raccourcis shell | `RACCOURCIS.md` |
| Hiérarchie des rôles (détaillée) | `HIERARCHIE.md` |
| Architecture globale | `docs/architecture/overview.md` |
| Schéma BDD | `docs/architecture/database-schema.md` |
| Back-office | `docs/architecture/easyadmin.md` |
| Frontend | `docs/architecture/frontend.md` |
| Permissions fines | `docs/architecture/permissions-fines-formations.md` |
| Historique des révisions | `docs/architecture/decision-historique-revisions.md` |
| Gestion des stagiaires | `docs/architecture/proposition-gestion-stagiaires-formation.md` |
| SEO / performances | `docs/architecture/seo-performances.md` |
| Analytics (Matomo) | `docs/architecture/analytics.md` |
| Docker | `docs/devops/docker-setup.md` |
| CI/CD | `docs/devops/github-actions.md` |
| Déploiement VPS | `docs/devops/vps-preprod.md` |
| Mémoire / décisions récentes | `.claude/MEMORY.md` |
| Journal des changements | `documentations-dev/` |
| Tâches réalisées avec Claude | `.claude-tasks/` |

Bienvenue sur le projet, et bon travail ! Pour toute question sur l'historique : Michaël (Mikhawa).
