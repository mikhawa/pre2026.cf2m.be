# 211 — Déploiement de la SPA React compilée en production

- **Modèle** : Opus
- **Justification** : suite directe de la tâche 210 (même session), simple report en prod.

## Fichiers modifiés
- `.github/workflows/deploy-prod.yml` — job `frontend-build`, envoi SCP vers `var/spa-dist`, installation dans `public/spa/` et `public/spa-assets/` après AssetMapper (identique à la préprod)
- `docs/architecture/decision-ilots-react.md`, `.gitignore` — mention de la prod

## Résumé
La SPA est désormais servie sur `/spa/` en prod aussi (`production.cf2m.be/pre2026.cf2m.be`), site Twig inchangé.

## Résultat
YAML valide. Non testé en conditions réelles (nécessite un push sur `production`).
