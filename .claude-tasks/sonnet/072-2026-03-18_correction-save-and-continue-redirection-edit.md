# 072 — Correction redirection "Sauvegarder et continuer" (Formation, Works, Pages)

**Modèle** : Sonnet
**Justification** : Correction d'un bug dans la configuration des actions EasyAdmin

## Problème

Le bouton "Sauvegarder et continuer les changements" (`SAVE_AND_CONTINUE`) redirigait vers le dashboard au lieu de rester sur la page d'édition, sur les 3 controllers : Formation, Works, Page.

## Cause racine

Dans `configureActions()`, l'appel :
```php
->setHtmlAttributes(['data-ea-btn' => 'continue'])
```
**remplace** (pas merge) les attributs HTML définis par EasyAdmin :
```php
['name' => 'ea[newForm][btn]', 'value' => 'saveAndContinue']
```

Sans ces attributs, `getRedirectResponseAfterSave()` reçoit `$submitButtonName = null` et tombe sur le `default` du `match` → redirection vers le dashboard.

## Fichiers modifiés

- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `src/Controller/Admin/PageCrudController.php`

## Correction

Suppression de `->setHtmlAttributes(['data-ea-btn' => 'continue'])` dans les 3 controllers. Les attributs `name`/`value` d'EasyAdmin sont désormais préservés, et `SAVE_AND_CONTINUE` redirige correctement vers `Action::EDIT`.
