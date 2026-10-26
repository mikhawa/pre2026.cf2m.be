# 134 — Installation des îlots React

**Date** : 2026-10-02 15:43
**Fichiers principaux** : `importmap.php`, `config/packages/react.yaml`, `package.json`,
`babel.config.json`, `assets/react/`, `docker-compose.yml`, `.github/workflows/symfony.yml`

## Résumé

Mise en place de React (îlots dans les pages Twig) via Symfony UX React. Les sources JSX sont
dans `assets/react/src/`, compilées par Babel dans `assets/react/build/` (versionné), et React
est servi par l'importmap. Un îlot de démonstration (`Demo/HelloIsland`) est affiché sur la page
d'accueil en environnement dev uniquement. Service Docker `node` pour la compilation en continu,
contrôle en CI que le JSX compilé est bien commité.

## Raison

Le design Figma doit être intégré avec React pour les parties interactives, sans réécrire le
site ni casser l'existant (sécurité, formulaires, SEO, EasyAdmin). Voir
`docs/architecture/decision-ilots-react.md` pour les options évaluées et les règles d'usage.
