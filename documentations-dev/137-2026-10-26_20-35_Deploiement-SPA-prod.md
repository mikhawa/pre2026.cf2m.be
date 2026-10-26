# 137 — Déploiement de la SPA React compilée en production

- **Date** : 2026-10-26
- **Fichiers** : `.github/workflows/deploy-prod.yml`, `docs/architecture/decision-ilots-react.md`, `.gitignore`
- **Résumé** : même mécanisme que la préprod (voir 136) — build Vite en CI, envoi SCP, installation dans `public/spa/` et `public/spa-assets/`.
- **Raison** : demande de l'utilisateur d'envoyer aussi la SPA en production.
