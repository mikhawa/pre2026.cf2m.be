# 176 — Fix AJAX inscriptions/contact non mis à jour pour ROLE_PEDAGO

**Modèle** : Haiku
**Justification** : Correction simple d'attribut de sécurité

## Problème
Les endpoints AJAX `/admin/inscription/{id}/traitement-info` et
`/admin/contact-message/{id}/lecture-info` retournaient 403 pour les
utilisateurs `ROLE_PEDAGO` car les controllers utilisaient `#[IsGranted('ROLE_ADMIN')]`.
`ROLE_PEDAGO` n'hérite pas de `ROLE_ADMIN` dans la hiérarchie.

## Cause
Les CrudControllers correspondants utilisent correctement `CONTENT_MANAGER`
(voter `ContentManagerVoter` → accordé à `ROLE_ADMIN` **et** `ROLE_PEDAGO`),
mais les controllers Ajax avaient été créés avec `ROLE_ADMIN` par erreur.

## Correction
Remplacement de `#[IsGranted('ROLE_ADMIN')]` par `#[IsGranted('CONTENT_MANAGER')]`

## Fichiers modifiés
- `src/Controller/Admin/InscriptionAjaxController.php`
- `src/Controller/Admin/ContactMessageAjaxController.php`
