# 166 — AJAX mise à jour traitement inscription (temps réel)

**Modèle** : Sonnet
**Justification** : Controller métier + interception fetch + manipulation DOM

## Fichiers modifiés / créés
- `src/Controller/Admin/InscriptionAjaxController.php` — créé
- `assets/inscription_treat.js` — créé
- `assets/admin.js` — ajout `import './inscription_treat.js'`

## Résumé
Quand un admin bascule le toggle "Traitée" dans la liste EasyAdmin des inscriptions,
les colonnes "Traitée le" et "Traitée par" ainsi que le badge du menu latéral
se mettent à jour sans rechargement de page.

## Approche (v2 — event delegation)
1. `document.addEventListener('change', ...)` — event delegation sur document.
   Détecte les changements sur `checkbox` à l'intérieur de `td[data-column="treat"]`.
2. Récupère l'entityId depuis `tr[data-id]` parent.
3. `setTimeout(600ms)` pour laisser le PATCH EasyAdmin se terminer côté serveur.
4. Appelle `GET /admin/inscription/{id}/traitement-info`.
5. `InscriptionAjaxController` retourne `{treatAt, treatAtIso, treatBy, untreatedCount}`.
6. Met à jour `td[data-column="treatAt"]`, `td[data-column="treatBy"]`
   et le badge `a.menu-item-contents[href*="InscriptionCrudController"] .menu-item-badge`.

## Points techniques
- Event delegation sur document : survit aux navigations Turbo sans réinitialisation
- `<time datetime="ISO">` reconstruit pour respecter la sémantique EasyAdmin
- `escapeHtml()` sur le nom d'utilisateur (XSS safe)
- `public/assets/` vidé + cache Symfony effacé pour forcer rechargement en dev
- Protégé par `#[IsGranted('ROLE_ADMIN')]`
