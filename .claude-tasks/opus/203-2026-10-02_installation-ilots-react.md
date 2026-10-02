# 203 — Installation des îlots React (Symfony UX React + Babel)

**Modèle utilisé** : Opus

**Justification** : décision d'architecture frontend (intégration de React dans une stack
AssetMapper sans build step), impact sur Docker, la CI et le déploiement.

## Contexte

Le design Figma doit être intégré avec React. Option retenue avec l'équipe : des **îlots React
dans les pages Twig** (Symfony UX React), plutôt qu'une SPA séparée ou un passage à Webpack Encore.
Décision détaillée : `docs/architecture/decision-ilots-react.md`.

## Fichiers modifiés

- `composer.json`, `composer.lock`, `symfony.lock`, `config/bundles.php`, `config/reference.php` — `symfony/ux-react` ^3.5.1
- `importmap.php` — `@symfony/ux-react` (loader.js), `react`, `react/jsx-runtime`, `react-dom`, `react-dom/client`, `scheduler` (ajoutés à la main, voir plus bas)
- `assets/app.js` — `registerReactControllerComponents()` (recette Flex)
- `assets/controllers.json` — contrôleur Stimulus `@symfony/ux-react`
- `config/packages/react.yaml` (nouveau) — `controllers_path: assets/react/build/islands`
- `config/packages/asset_mapper.yaml` — exclusion de `assets/react/src/` (JSX brut)
- `package.json`, `package-lock.json`, `babel.config.json` (nouveaux) — Babel (`@babel/preset-react`, runtime automatique), scripts `react:build` / `react:watch`
- `assets/react/src/islands/Demo/HelloIsland.jsx`, `assets/react/src/components/Counter.jsx` (nouveaux) — îlot de démonstration
- `assets/react/build/**` (nouveau, généré) — JSX compilé, versionné car le VPS n'a pas Node
- `templates/home/index.html.twig` — îlot de démonstration affiché en dev uniquement
- `docker-compose.yml` — service `node` (Babel en watch, polling pour les volumes Windows)
- `.github/workflows/symfony.yml` — compilation Babel + échec si `assets/react/build/` n'est pas à jour
- `.gitignore` (`/node_modules/`), `.gitattributes` (`assets/react/build/**` marqué comme généré)
- `docs/architecture/decision-ilots-react.md` (nouveau), `docs/architecture/overview.md`, `docs/architecture/frontend.md`, `docs/devops/docker-setup.md`, `CLAUDE.md`

## Résumé

- Babel ne fait que transformer le JSX (`src/` → `build/`) ; React est servi par l'importmap comme
  les autres dépendances, Stimulus monte les composants (compatible Turbo).
- **Bug AssetMapper 7.4.19** : `importmap:require react-dom` échoue (« Unable to find the latest
  version for package "react" ») car jsDelivr renvoie la dépendance encodée `react@%5E19.3.0` et
  AssetMapper la ré-encode (`%255E…` → version `null`). La recette Flex s'est donc arrêtée en
  cours de route : entrées importmap et `controllers.json` complétées à la main, puis
  `importmap:install`. Attention : avec AssetMapper, `@symfony/ux-react` doit pointer sur
  `dist/loader.js` (et non `register_controller.js`, réservé à Encore/Vite).
- À noter : les fichiers déjà en CRLF dans l'index (`CLAUDE.md`, `composer.json`,
  `docker-compose.yml`, `docs/…`) sont normalisés en LF par `.gitattributes` (`eol=lf`) à la
  modification → diff « fichier entier » sur GitHub (option « Hide whitespace » pour relire).

## Résultat

- `debug:asset-map` : îlots compilés présents, `assets/react/src/` exclu.
- Page d'accueil (dev) rendue dans Chrome headless : l'îlot affiche « Îlot React actif — bonjour
  CF2m ! » et son bouton — chaîne Twig → Stimulus → React validée.
- PHPUnit : OK (177 tests, 404 assertions).
- Non vérifié ici : clic sur le compteur et navigation Turbo aller-retour (à confirmer dans le
  navigateur), service Docker `node` (non démarré), exécution de la CI GitHub.
