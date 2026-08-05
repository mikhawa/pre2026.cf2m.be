---
name: easyadmin-js-dom
description: Conventions JS/DOM EasyAdmin 4 — sélecteurs de menu, structure des badges, CSS via AssetMapper, event delegation Turbo
metadata:
  type: project
---

## EasyAdmin 4 — conventions JS/DOM importantes
- **Routes nommées** : les liens du menu utilisent `/admin/{entite}` (ex: `/admin/inscription`), PAS `?crudControllerFqcn=...`. Sélecteur correct : `#main-menu a.menu-item-contents[href*="/admin/inscription"]`
- **Structure badge menu** : `<span class="menu-item-badge">` est enfant direct du `<a class="menu-item-contents">`, PAS du `<span class="menu-item-label">`
- **Cellules tableau** : `<td data-column="fieldProperty">` et `<tr data-id="entityId">` — data attributes natifs EasyAdmin 4, utilisables pour cibler les cellules en JS
- **CSS admin via AssetMapper** : utiliser `import './styles/admin.css'` dans `admin.js`, PAS `addHtmlContentToHead(...)` dans `configureAssets()` (chemin non-fingerprinted → 404 avec assets compilés)
- **Event delegation toggle** : écouter `change` sur `document`, vérifier `checkbox.closest('td[data-column="treat"]')` — survit aux navigations Turbo sans réinitialisation
- **Assets compilés dev** : supprimer `public/assets/` + `cache:clear` après chaque modif JS/CSS pour forcer la recompilation
