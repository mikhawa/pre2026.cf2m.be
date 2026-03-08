# 040 — Correction CSRF invalide + vérification fixtures

**Date** : 2026-03-08 17:00
**Type** : Correction de bug

## Problèmes traités

### 1. Jeton CSRF invalide sur /connexion

#### Cause
Le projet utilise `SameOriginCsrfTokenManager` de Symfony (configuration stateless basée sur cookie), configuré dans `config/packages/csrf.yaml` avec `stateless_token_ids: [submit, authenticate, logout]`.

Ce mécanisme nécessite que le code JavaScript `assets/controllers/csrf_protection_controller.js` soit exécuté pour :
1. Intercepter l'événement `submit` du formulaire
2. Remplacer le token CSRF (initialement issu de la session Symfony) par un token aléatoire en base64
3. Écrire ce token dans un cookie

Sans cet import, Symfony reçoit un token de session mais attend un token stateless → "Jeton CSRF invalide".

#### Correction
`assets/app.js` : ajout de l'import explicite du controller CSRF.

**Avant :**
```js
import './stimulus_bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import 'bootstrap';
```

**Après :**
```js
import './stimulus_bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import './styles/app.css';
import 'bootstrap';
import './controllers/csrf_protection_controller.js';
```

**Note** : `data-turbo="false"` était déjà correctement positionné sur le formulaire de login. L'event listener du controller CSRF s'active pour tous les formulaires ayant `input[name="_csrf_token"]`, indépendamment de Turbo.

### 2. Fixtures

#### Diagnostic
Les fixtures fonctionnent correctement. `php bin/console doctrine:fixtures:load --no-interaction` se termine avec code de sortie 0 en ~15 secondes.

La commande ne produisait pas le message habituel "Database fixtures loaded successfully" dans certains contextes Docker — ce n'est pas une erreur.

#### État après chargement
| Table | Enregistrements |
|-------|----------------|
| user | 33 |
| formation | 10 |
| works | ~31 |
| inscription | ~34 |
| partenaire | 8 |
| page | 6 |
| comment | ~53 |
| rating | ~51 |

Super admin `mikhawa@cf2m.be` avec `ROLE_SUPER_ADMIN` présent.

## Fichier modifié

- `assets/app.js`
