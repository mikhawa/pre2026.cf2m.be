# Tâche : Squelette de la SPA React (Vite), service Docker et build CI

**Numéro** : 207
**Date** : 2026-10-15
**Modèle utilisé** : Opus
**Justification du modèle** : mise en place de l'architecture cible (option B), touche Docker, CI et backend
**Complexité** : Moyenne
**Fichiers concernés** : `frontend/` (nouveau), `src/Controller/Api/StatutController.php`,
`tests/Controller/Api/StatutControllerTest.php`, `docker-compose.yml`, `.github/workflows/symfony.yml`,
`docs/architecture/decision-ilots-react.md`, `docs/devops/docker-setup.md`

## Contexte nécessaire
Étape 1 du plan de migration vers une SPA React séparée (décision du 2026-10-15). Il n'existait ni Vite ni
projet Node pour une SPA ; le VPS n'a pas Node. Le site Twig + îlots reste intact.

## Objectif
Une SPA Vite + React 19 qui tourne en dev dans Docker, appelle Symfony via `/api` et est compilée en CI.

## Résultat
- `frontend/` : Vite 7, React 19.3.0 (aligné sur `importmap.php`), react-router 7 ; pages Accueil (appelle
  `/api/statut`) et PageIntrouvable ; `vite.config.js` relaie `/api`, `/admin`, `/login`, `/logout`, `/uploads`,
  `/images` vers nginx (même origine).
- `GET /api/statut` (`StatutController`) + premier WebTestCase du projet.
- Service Docker `frontend` (port 5173) ; le service `node` des îlots est conservé.
- Job CI `frontend-build` : `npm ci`, `npm run build`, artefact `frontend-dist`.
- Vérifié en local : build Vite OK, http://localhost:5173 sert la SPA, `/api/statut` via le relais → `{"statut":"ok"}`.
- Reste à faire : déploiement de `dist/` sur le VPS, étape 2 (session, `/api/layout`, portage Header/Footer/Hero).
