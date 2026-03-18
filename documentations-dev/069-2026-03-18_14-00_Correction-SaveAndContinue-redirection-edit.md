# 069 — Correction "Sauvegarder et continuer" → reste sur la page d'édition

**Date** : 2026-03-18 14:00
**Fichiers modifiés** :
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `src/Controller/Admin/PageCrudController.php`

## Résumé

Le bouton "Sauvegarder et continuer les changements" (`SAVE_AND_CONTINUE`) redirigait vers le dashboard au lieu de rester sur la page d'édition, pour Formation, Works et Pages.

## Cause

`Action::setHtmlAttributes()` **remplace** tout le tableau d'attributs (pas de merge). L'appel :
```php
->setHtmlAttributes(['data-ea-btn' => 'continue'])
```
écrasait les attributs `name="ea[newForm][btn]"` et `value="saveAndContinue"` qu'EasyAdmin injecte par défaut sur le bouton SAVE_AND_CONTINUE.

Sans ces attributs, `getRedirectResponseAfterSave()` ne peut pas identifier le bouton pressé → redirige vers le dashboard.

## Correction

Suppression de `->setHtmlAttributes(['data-ea-btn' => 'continue'])` dans les 3 controllers. EasyAdmin préserve ses attributs natifs et redirige correctement vers la page d'édition de l'entité après sauvegarde.
