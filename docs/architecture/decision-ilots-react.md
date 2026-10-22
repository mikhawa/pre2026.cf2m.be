# Décision architecturale — Frontend React (SPA séparée, Symfony en backend)

**Date** : 2026-10-15 (révision de la décision du 2026-10-02)
**Statut** : Adopté — remplace l'option A (îlots React dans Twig, retirée du code), migration en cours
**Auteur** : Soliman Azoz / Claude Opus

> Le nom du fichier est conservé pour ne pas casser les liens existants ; il décrit désormais l'option B.

---

## Contexte

Le nouveau design du site est réalisé sous Figma et doit être intégré avec **React**. Le site existant repose
sur Twig + AssetMapper/ImportMap + Stimulus + Turbo + Bootstrap, et tout le fonctionnel (sécurité, voters,
formulaires avec CSRF/Turnstile, SEO, EasyAdmin) passe par Symfony.

Le 2026-10-02, l'option A (îlots React montés dans Twig) avait été retenue et mise en place
(`feature/32-react-islands`, `feature/33-design-tokens-figma`). Après nouvelle discussion le 2026-10-15,
l'équipe passe à l'option B : **un frontend React autonome, Symfony restant le backend**.

## Options évaluées

| Option | Principe | Verdict |
|---|---|---|
| A. Îlots React dans Twig | Twig rend la page ; des composants React sont montés à des endroits précis via `react_component()` (Symfony UX React) | **Remplacée** (était retenue le 2026-10-02) |
| **B. SPA React séparée + API Symfony** | Application React autonome (Vite) ; Symfony conserve le métier, la sécurité, la BDD et EasyAdmin, et expose une API JSON | **Retenue** |
| C. Rester en Twig | Intégrer Figma uniquement avec Twig + CSS + Stimulus | Rejetée : pas de React pour les interactions riches |

## Décision

1. **Symfony reste le backend** : entités, services, repositories, voters, Doctrine/MariaDB, emails,
   EasyAdmin. Les règles métier et les contrôles de droits restent côté serveur (**aucun test de rôle dans React**).
2. **Le frontend public devient une SPA React 19 (Vite)**, dans un dossier dédié du dépôt (`frontend/`).
3. **Symfony expose une API JSON** (`/api/...`) : contrôleurs renvoyant `JsonResponse` / `$this->json(...)`
   avec groupes de sérialisation, protégés par `#[IsGranted]` / voters. Uniquement des tableaux/DTO, jamais d'entité.
4. **EasyAdmin reste en Twig** sur `/admin` (aucune réécriture du back-office).
5. **Le build de la SPA se fait en CI** (GitHub Actions), le VPS n'ayant pas Node : les fichiers compilés sont
   envoyés sur le serveur et servis par Apache/Nginx, Symfony gardant `/api` et `/admin`.

## Points à trancher (recommandation entre parenthèses)

| Sujet | Choix possibles | Recommandation |
|---|---|---|
| Authentification | Session Symfony par cookie (même domaine) / JWT | **Session par cookie, même domaine** : réutilise le firewall, la 2FA et les voters existants ; pas de jeton stocké côté navigateur |
| CSRF | Jeton fourni par l'API / cookie `SameSite=Strict` + en-tête | Jeton CSRF fourni par un point d'API, envoyé en en-tête sur les écritures |
| SEO (pages formations, travaux) | SPA pure / pré-rendu des pages publiques / SSR (Next.js) | À décider : une SPA pure est mal indexée, à éviter pour les pages de formations |
| Hébergement de la SPA | Même domaine (`/` → SPA, `/api` et `/admin` → Symfony) / sous-domaine | Même domaine (pas de CORS, cookies simples) |
| Formulaires (contact, inscription, mot de passe oublié) | Réécriture React + validation API | Validation Symfony (`Validator`) conservée côté API ; Turnstile vérifié côté serveur |

## Conséquences sur l'existant (appliquées le 2026-10-15)

L'option A a été **retirée entièrement** dès l'adoption de l'option B :

| Élément de l'option A | Devenir |
|---|---|
| Composants JSX Header, Footer, Hero, Logo, ThemeToggle, NavDropdown, UserMenu, Icon, `useCurrentPath` | **Déplacés** dans `frontend/src/` (`layout/`, `sections/`, `components/`, `hooks/`), à adapter à la SPA (étape 2) |
| Tokens Figma (`tokens.css`, `layout.css`) | **Déplacés** dans `frontend/src/styles/` |
| `LayoutPropsBuilder` (+ test) | **Conservé** : servira le point d'API `GET /api/layout` (étape 2) |
| `LayoutExtension` (fonctions Twig `header_props()`…), replis Twig `_header_fallback` / `_hero_fallback` | Supprimés |
| `symfony/ux-react`, `config/packages/react.yaml`, entrées React de `importmap.php` | Supprimés |
| Babel (`package.json`, `babel.config.json` à la racine), `assets/react/` (sources et `build/`) | Supprimés |
| Étape CI « Compile React islands » | Supprimée |
| `base.html.twig`, `home/index.html.twig` | Revenus à l'en-tête, au pied de page et au hero **Twig** de `main` (polices Poppins/Montserrat conservées) |

Restent à faire au fil de la migration : templates Twig publics, Stimulus, Turbo et formulaires Symfony publics
(remplacés page par page ; EasyAdmin et les emails restent en Twig), déploiement de la SPA dans
`deploy-preprod.yml` / `deploy-prod.yml`.

## Migration (proposition, par étapes)

1. ✅ Squelette `frontend/` (Vite + React 19), service Docker, build en CI — branche `feature/34-spa-react` (voir ci-dessous).
2. Authentification par session + points d'API `/api/me` et `/api/layout` ; brancher Header/Footer/Hero (déjà dans `frontend/src/`) sur ces points.
3. Pages publiques en lecture (accueil, formations, travaux, pages statiques) via API.
4. Formulaires (contact, inscription, connexion/2FA, réinitialisation du mot de passe, profil).
5. Suppression de Stimulus/Turbo et des templates publics devenus inutiles (Symfony UX React et Babel : déjà retirés).

Tant que la migration n'est pas terminée, le site Twig actuel (sans îlots) reste en production.

## Organisation de la SPA (`frontend/`)

```
frontend/
├── package.json, package-lock.json   ← dépendances de la SPA (versionnées)
├── vite.config.js                    ← serveur de dev 5173 + relais des routes Symfony vers nginx
├── index.html                        ← page unique, monte <App /> dans #root
└── src/
    ├── main.jsx, App.jsx             ← point d'entrée et routes (react-router)
    ├── api/client.js                 ← appels à l'API Symfony (même origine, cookie de session)
    ├── pages/                        ← une page par route
    ├── layout/                       ← Header, Footer (maquette Figma, repris de l'option A)
    ├── sections/                     ← blocs de page (Hero…)
    ├── components/, hooks/           ← composants et hooks partagés
    └── styles/                       ← tokens.css, layout.css (tokens Figma)
```

- **Dev** : `docker compose up -d` → http://localhost:5173. Vite relaie `/api`, `/admin`, `/login`, `/logout`,
  `/uploads`, `/images` vers nginx : SPA et API partagent la même origine.
- **API** : contrôleurs dans `src/Controller/Api/`, routes `/api/...`, réponses JSON. Premier point : `GET /api/statut`
  (`StatutController`, testé par `tests/Controller/Api/StatutControllerTest.php`).
- **CI** : le job `frontend-build` de `symfony.yml` fait `npm ci` + `npm run build` et publie `frontend/dist`
  (artefact `frontend-dist`). `dist/` n'est pas versionné. Le déploiement sur le VPS reste à faire.
- Imports **sans** extension (Vite résout les `.jsx`).

## API (backend Symfony) — API Platform

**API Platform 5** est installé **côté backend uniquement** (`api-platform/symfony`, `api-platform/doctrine-orm` dans
`composer.json`). La SPA n'installe rien : elle appelle `/api/...` avec `fetch` (`frontend/src/api/client.js`).

- **Configuration** : `config/packages/api_platform.yaml` — formats `jsonld` et `json` (la SPA demande
  `Accept: application/json`), `stateless: false` (authentification par **session** Symfony), documentation
  `/api/docs` en dev uniquement.
- **Jamais d'entité exposée directement** : une ressource = un DTO dans `src/ApiResource/` + un provider dans
  `src/State/` qui réutilise les repositories et ne renvoie que les champs publics.
- **Ressources disponibles** :

| Ressource | Routes | Fichiers |
|---|---|---|
| Formation (lecture) | `GET /api/formations`, `GET /api/formations/{slug}` | `FormationResource`, `FormationProvider` (formations `published` / `recruiting` uniquement, brouillon → 404) |

- **Cas particuliers** (`/api/statut`, futurs `/api/me`, `/api/layout`, contact avec Turnstile) : contrôleurs classiques
  dans `src/Controller/Api/`, ou ressource API Platform avec processeur, au cas par cas.
- Les valeurs `null` sont omises du JSON (comportement par défaut d'API Platform) : côté React, traiter un champ
  absent comme `null` (ex. `logoUrl`).

## Historique

- **2026-10-02** : option A adoptée (îlots React via Symfony UX React, JSX compilé par Babel, sortie versionnée
  dans `assets/react/build/`, React servi par l'importmap). Voir `documentations-dev/134-...` et `135-...`.
- **2026-10-15** : passage à l'option B après nouvelle discussion d'équipe ; Symfony conservé comme backend.
  Le même jour, toute l'infrastructure de l'option A est retirée (composants récupérés dans `frontend/`).
