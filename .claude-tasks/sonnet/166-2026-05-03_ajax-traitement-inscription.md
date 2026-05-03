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

## Approche
1. `inscription_treat.js` enveloppe `window.fetch` pour intercepter le PATCH d'EasyAdmin
   (détecté via `url.includes('InscriptionCrudController')` + `method === 'PATCH'`).
2. Après résolution du PATCH (réponse serveur reçue), appelle
   `GET /admin/inscription/{id}/traitement-info` — aucune race condition possible.
3. `InscriptionAjaxController` retourne `{treatAt, treatAtIso, treatBy, untreatedCount}`.
4. Le JS met à jour `td[data-column="treatAt"]`, `td[data-column="treatBy"]`
   et le badge `a.menu-item-contents[href*="InscriptionCrudController"] .menu-item-badge`.

## Points techniques
- `_originalFetch` conservé pour appeler l'endpoint sans re-entrer dans l'intercepteur
- `<time datetime="ISO">` reconstruit pour respecter la sémantique EasyAdmin
- `escapeHtml()` sur le nom d'utilisateur (XSS safe)
- Modules ES6 : pas de double-wrapping possible (cache navigateur)
- Protégé par `#[IsGranted('ROLE_ADMIN')]`
