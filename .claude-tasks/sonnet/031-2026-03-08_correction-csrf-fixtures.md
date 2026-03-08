# Tâche 031 — Correction CSRF invalide et vérification fixtures

**Date** : 2026-03-08
**Modèle** : Sonnet (investigation multi-fichiers, diagnostic et correction)
**Justification** : Controller, session, configuration Symfony Security — niveau Sonnet

## Problèmes investigués

### 1. Jeton CSRF invalide sur /connexion
**Cause racine** : Le fichier `assets/controllers/csrf_protection_controller.js` est déclaré comme Stimulus lazy controller. Ses event listeners `document.addEventListener('submit', ...)` ne s'activent que si le module est importé. Or, il n'était **pas importé** dans `assets/app.js`.

Le `csrf_protection_controller.js` est le mécanisme JS du `SameOriginCsrfTokenManager` (stateless, basé cookie). La configuration Symfony dans `config/packages/csrf.yaml` déclare `authenticate` dans `stateless_token_ids`, ce qui signifie que Symfony attend un token CSRF en format base64 dans un cookie. Sans l'import JS, le token n'était jamais transformé → "Jeton CSRF invalide".

**Correction** : Ajout de `import './controllers/csrf_protection_controller.js';` dans `assets/app.js`.

### 2. Fixtures
**Diagnostic** : Les fixtures fonctionnent correctement. `doctrine:fixtures:load --no-interaction` se termine avec code de sortie 0. La base est correctement peuplée (33 users, 10 formations, 31 works, 34 inscriptions, 8 partenaires, 6 pages, 53 comments, 51 ratings).

Le problème perçu était un **problème d'affichage** : avec `flush_once: true` dans `zenstruck_foundry.yaml`, la commande se termine sans message visible dans certains terminaux Docker. Le process se complète en ~15 secondes.

## Fichiers modifiés

- `assets/app.js` : ajout de l'import du controller CSRF

## Fichiers consultés (diagnostic)

- `config/packages/security.yaml`
- `config/packages/csrf.yaml`
- `config/packages/ux_turbo.yaml`
- `config/packages/framework.yaml`
- `config/packages/zenstruck_foundry.yaml`
- `templates/security/login.html.twig`
- `assets/controllers/csrf_protection_controller.js`
- `assets/app.js`
- `src/DataFixtures/AppFixtures.php`
- `src/Story/AppStory.php`
- Toutes les factories (User, Formation, Works, Inscription, Comment, Rating, Partenaire, Page)

## Résultat

- Fixtures : EXIT:0, base peuplée correctement
- CSRF : import ajouté, le SameOriginCsrfTokenManager peut maintenant fonctionner
