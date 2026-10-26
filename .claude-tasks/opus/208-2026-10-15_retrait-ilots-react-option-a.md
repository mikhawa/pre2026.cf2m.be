# Tâche : Retrait des îlots React (option A) au profit de la SPA (option B)

**Numéro** : 208
**Date** : 2026-10-15
**Modèle utilisé** : Opus
**Justification du modèle** : refactoring transversal (Composer, importmap, Twig, CI, Docker) lié à un changement d'architecture
**Complexité** : Moyenne
**Fichiers concernés** : `assets/react/` (supprimé), `frontend/src/`, `templates/base.html.twig`, `templates/home/index.html.twig`,
`composer.json`, `importmap.php`, `assets/app.js`, `config/`, `.github/workflows/symfony.yml`, `docs/`

## Contexte nécessaire
L'option A (îlots React dans Twig) est remplacée par l'option B (SPA React séparée, Symfony en backend).
Son infrastructure n'a plus lieu d'être.

## Résultat
- Composants Figma (Header, Footer, Hero, Logo, ThemeToggle, NavDropdown, UserMenu, Icon, useCurrentPath) et styles
  `tokens.css` / `layout.css` **déplacés** dans `frontend/src/` (imports adaptés à Vite), à brancher à l'étape 2.
- Supprimés : `symfony/ux-react`, `config/packages/react.yaml`, entrées React de l'importmap, `assets/react/`,
  Babel (`package.json`, `package-lock.json`, `babel.config.json`), `LayoutExtension`, replis Twig, étape CI Babel.
- `base.html.twig` et `home/index.html.twig` revenus à la version Twig de `main` (îlot de démo retiré), polices
  Poppins/Montserrat conservées ; coquille `Monterrat` corrigée dans `app.css`.
- `LayoutPropsBuilder` et son test conservés pour le futur `GET /api/layout`.
- Non fait (refusé par le contrôle de permissions) : retrait du service `node` dans `docker-compose.yml`.
