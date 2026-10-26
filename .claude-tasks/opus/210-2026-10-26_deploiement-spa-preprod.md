# 210 — Déploiement de la SPA React compilée sur la préprod

- **Modèle** : Opus
- **Justification** : CI/CD (niveau Sonnet) mais avec un choix d'architecture d'hébergement (cohabitation SPA / AssetMapper / site Twig) — traité directement.

## Fichiers modifiés
- `.github/workflows/deploy-preprod.yml` — job `frontend-build` (npm ci + build, `VITE_ROUTER_BASENAME=/spa`), envoi SCP vers `var/spa-dist`, installation dans `public/spa/` et `public/spa-assets/` après le `git pull`
- `frontend/vite.config.js` — `build.assetsDir: 'spa-assets'`
- `frontend/src/main.jsx` — `basename` du `BrowserRouter` lu depuis `VITE_ROUTER_BASENAME` (défaut `/`)
- `.gitignore` — `/public/spa/`, `/public/spa-assets/`
- `docs/architecture/decision-ilots-react.md` — section CI/déploiement

## Résumé
Schéma choisi par l'utilisateur : JS/CSS Vite dans `public/spa-assets/` (évite `public/assets/` d'AssetMapper),
point d'entrée dans `public/spa/index.html` → SPA accessible sur `/spa/`, site Twig inchangé. Préprod uniquement.

## Résultat
Build local vérifié (`dist/index.html` → `/spa-assets/index-*.js`, basename `/spa` intégré), YAML valide.
Non testé en conditions réelles (nécessite un push sur `preprod/*`). Pas de repli serveur pour les sous-routes.
