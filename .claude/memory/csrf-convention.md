---
name: csrf-convention
description: Mécanisme CSRF stateless du projet (SameOriginCsrfTokenManager) — import JS obligatoire sinon "Jeton CSRF invalide"
metadata:
  type: project
---

## Convention CSRF (SameOriginCsrfTokenManager)
Le projet utilise le mécanisme stateless de Symfony (`SameOriginCsrfTokenManager`) :
- `config/packages/csrf.yaml` : `stateless_token_ids: [submit, authenticate, logout]`
- `assets/controllers/csrf_protection_controller.js` : intercepte `submit`, remplace le token par un token base64, l'écrit dans un cookie
- **Ce fichier DOIT être importé dans `assets/app.js`** : `import './controllers/csrf_protection_controller.js';`
- Sans cet import, les formulaires retournent "Jeton CSRF invalide"
