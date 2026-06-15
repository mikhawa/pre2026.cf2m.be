# 115 — Fix : entityId parasite dans l'URL "Révisions en attente"

**Date :** 2026-06-15  
**Commit :** `3a9f69c`  
**Branche :** `feature/24-prepa-for-design`

---

## Problème

Depuis la page d'édition d'un Work (ex. `/admin/works/802/edit`), cliquer sur **"Révisions en attente"** dans le menu de gauche générait l'URL :

```
/admin/revisions-pendantes/revisions-en-attente?entityId=802
```

EasyAdmin tentait alors de charger une entité `Formation` avec l'id 802 (car `RevisionsPendantesController::getEntityFqcn()` retourne `Formation::class`), provoquant :

> The "App\Entity\Formation" entity with "id = 802" does not exist in the database.

## Cause racine

`AdminUrlGenerator` est **stateful** : il hérite le contexte de la requête courante. Lors de la construction du menu dans `DashboardController::configureMenuItems()`, l'`entityId=802` de la page en cours est conservé en état et injecté dans l'URL générée.

Le même problème existait dans `RevisionsPendantesController::revisionsPendantes()` pour la génération de la `returnUrl` (URL de retour après approbation/rejet).

## Correctif

Ajout de `->unset('entityId')` avant `->generateUrl()` dans les deux emplacements.

### `src/Controller/Admin/DashboardController.php`

```php
$revisionsPendantesUrl = $this->adminUrlGenerator
    ->unset('entityId')                          // ← ajouté
    ->setController(RevisionsPendantesController::class)
    ->setAction('revisionsPendantes')
    ->generateUrl();
```

### `src/Controller/Admin/RevisionsPendantesController.php`

```php
$returnUrl = $adminUrlGenerator
    ->unset('entityId')                          // ← ajouté
    ->setController(self::class)
    ->setAction('revisionsPendantes')
    ->generateUrl();
```

## Fichiers modifiés

- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/RevisionsPendantesController.php`
