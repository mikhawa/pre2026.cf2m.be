# Décision architecturale — Îlots React dans Twig

**Date** : 2026-10-02
**Statut** : Adopté (mise en place — branche `feature/32-react-islands`)
**Auteur** : Soliman Azoz / Claude Opus

---

## Contexte

Le nouveau design du site est réalisé sous Figma et doit être intégré avec **React** pour les parties
interactives. Le site existant repose sur Twig + AssetMapper/ImportMap + Stimulus + Turbo + Bootstrap,
**sans étape de build**, et tout le fonctionnel (sécurité, voters, formulaires avec CSRF/Turnstile, SEO,
EasyAdmin) passe par Symfony.

## Options évaluées

| Option | Principe | Verdict |
|---|---|---|
| **A. Îlots React dans Twig** | Twig rend la page ; des composants React sont montés à des endroits précis via `react_component()` (Symfony UX React) | **Retenue** |
| B. SPA React séparée + API | Application React autonome (Vite), Symfony réduit à une API JSON | Rejetée : réécriture complète (auth, CSRF, formulaires, SEO, déploiement) |
| C. Rester en Twig | Intégrer Figma uniquement avec Twig + CSS + Stimulus | Rejetée : pas de React pour les interactions riches |

Pour la compilation du JSX, trois variantes ont été comparées : remplacer AssetMapper par Webpack Encore
(migration lourde, et cohabiter avec AssetMapper chargerait Stimulus/Turbo en double), écrire sans JSX
(`htm`), ou **garder AssetMapper et compiler le JSX avec Babel** → retenu.

## Décision

1. **Symfony UX React** (`symfony/ux-react`) monte les composants via son contrôleur Stimulus :
   compatible Turbo (montage/démontage automatique), aucune modification de `base.html.twig`.
2. **React, react-dom et scheduler sont servis par l'importmap** (`importmap.php`), comme Bootstrap ou Turbo.
3. **Babel ne fait qu'une chose** : transformer `assets/react/src/**/*.jsx` en `assets/react/build/**/*.js`.
   Il ne regroupe rien : les `import 'react'` restent tels quels et sont résolus par l'importmap.
4. **Le JSX compilé est versionné** (`assets/react/build/`) : le déploiement (`deploy-preprod.yml`,
   `deploy-prod.yml`) fait `git pull` + `asset-map:compile` sur le VPS, qui n'a pas Node.
   La CI (`symfony.yml`) recompile et échoue si `build/` ne correspond pas aux sources.
5. **EasyAdmin reste en Twig** ; les formulaires Symfony (CSRF, Turnstile) restent des formulaires Symfony.

## Organisation des fichiers

```
assets/react/
├── src/                 ← sources JSX (on édite ici)
│   ├── islands/         ← composants appelés depuis Twig, un par îlot, rangés par domaine métier
│   │   └── Demo/HelloIsland.jsx
│   └── components/      ← composants réutilisables (boutons, cartes…) importés par les îlots
└── build/               ← sortie Babel : versionnée, ne jamais modifier à la main
```

- `config/packages/react.yaml` : `controllers_path` pointe sur `assets/react/build/islands`.
  `islands/Formation/FormationFilter.jsx` → `{{ react_component('Formation/FormationFilter', props) }}`.
- `config/packages/asset_mapper.yaml` exclut `assets/react/src/` (le navigateur ne lit pas le JSX).
- **Dans les imports entre fichiers, écrire l'extension `.js`** (nom du fichier compilé) :
  `import Counter from '../../components/Counter.js';`

## Règles d'usage

- **Props** : uniquement des tableaux/DTO sérialisables, jamais d'entité Doctrine ; elles sont visibles
  dans le HTML, donc jamais de secret ni de donnée personnelle non nécessaire. Les URL passent par `path()`.
- **Données dynamiques** : contrôleurs Symfony qui renvoient du JSON (`$this->json(...)` + groupes de
  sérialisation), protégés par `#[IsGranted]` / voters ; jeton CSRF transmis en prop pour les écritures.
- **Choisir l'outil** : contenu statique → Twig + CSS ; comportement simple → Stimulus ;
  interaction riche (filtres en direct, recherche, formulaires multi-étapes) → îlot React.
- Garder `react`, `react/jsx-runtime`, `react-dom` et `react-dom/client` **à la même version**.

## Flux de travail

```bash
docker compose up -d          # le service node lance « npm run react:watch »
# … éditer assets/react/src/ …
npm run react:build           # avant de commiter (ou laisser le watch tourner)
git add assets/react/src assets/react/build
```

## Point d'attention : AssetMapper 7.4.19

`php bin/console importmap:require react-dom` échoue (« Unable to find the latest version for package
"react" ») : jsDelivr renvoie des dépendances encodées (`react@%5E19.3.0`) qu'AssetMapper ré-encode.
Contournement : ajouter les entrées à la main dans `importmap.php` puis `php bin/console importmap:install`.
Pour monter de version : modifier les versions dans `importmap.php`, supprimer `assets/vendor/react*`
et `assets/vendor/scheduler`, puis relancer `importmap:install`.

## Hors périmètre (prochaines étapes)

ESLint/Prettier, tests Vitest + React Testing Library, tokens de design Figma, premiers îlots réels.
