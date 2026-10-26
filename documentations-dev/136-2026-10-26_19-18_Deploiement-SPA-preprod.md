# 136 — Déploiement de la SPA React compilée sur la préprod

- **Date** : 2026-10-26
- **Fichiers** : `.github/workflows/deploy-preprod.yml`, `frontend/vite.config.js`, `frontend/src/main.jsx`, `.gitignore`, `docs/architecture/decision-ilots-react.md`
- **Résumé** : la CI préprod compile la SPA (Vite, Node 24) et l'envoie par SCP ; le script SSH l'installe dans `public/spa/index.html` (servie sur `/spa/`) et `public/spa-assets/` (JS/CSS).
- **Raison** : le VPS n'a pas Node ; `public/assets/` est réservé à AssetMapper, d'où le dossier `spa-assets/`. Le site Twig reste en place pendant la migration.
