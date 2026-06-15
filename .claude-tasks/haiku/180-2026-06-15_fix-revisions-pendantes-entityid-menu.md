# 180 — Fix : entityId parasite dans l'URL "Révisions en attente"

**Modèle** : Haiku  
**Justification** : Correction d'un paramètre URL hérité — simple, ciblée, sans logique métier.

## Problème

Depuis la page d'édition d'un Work (ex. `/admin/works/802/edit`), cliquer sur "Révisions en attente" dans le menu générait l'URL `/admin/revisions-pendantes/revisions-en-attente?entityId=802`. EasyAdmin tentait alors de charger une entité `Formation` avec l'id 802, provoquant l'erreur :

> The "App\Entity\Formation" entity with "id = 802" does not exist.

## Cause

`AdminUrlGenerator` est stateful et hérite les paramètres de la requête courante. Sans appel explicite à `->unset('entityId')`, l'`entityId` de la page en cours (celui du Work) se retrouve dans l'URL générée.

## Correctif

Ajout de `->unset('entityId')` avant `->generateUrl()` dans les deux endroits concernés.

## Fichiers modifiés

- `src/Controller/Admin/DashboardController.php` — génération de l'URL du menu
- `src/Controller/Admin/RevisionsPendantesController.php` — génération de la `returnUrl`
