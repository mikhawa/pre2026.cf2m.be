# 110 — Approuver/Rejeter/Restaurer Works ouverts à ROLE_FORMATEUR

**Modèle** : Haiku
**Justification** : Changements de permission simples sur trois méthodes.

## Fichiers modifiés
- `src/Controller/Admin/WorksCrudController.php`

## Résumé

Les actions approuver, rejeter et restaurer dans l'historique Works passent de `ROLE_ADMIN` à `ROLE_FORMATEUR`.

- `approuverHistoriqueWorks()` : `ROLE_ADMIN` → `ROLE_FORMATEUR`
- `rejeterHistoriqueWorks()` : `ROLE_ADMIN` → `ROLE_FORMATEUR`
- `restaurerHistoriqueWorks()` : `ROLE_ADMIN` → `ROLE_FORMATEUR`
