# 122 — Correction : ROLE_FORMATEUR → révision PENDING pour Formation

**Modèle** : Sonnet
**Justification** : Correction de logique métier dans un controller

## Problème

`FormationCrudController::updateEntity()` utilisait `isGranted('ROLE_FORMATEUR')` comme seuil d'auto-approbation. Or, dans la hiérarchie Symfony, `isGranted('ROLE_FORMATEUR')` retourne `true` pour ROLE_FORMATEUR, ROLE_ADMIN et ROLE_SUPER_ADMIN — ce qui faisait passer les modifications des formateurs directement en AUTO_APPROVED sans révision.

## Fichiers modifiés

- `src/Controller/Admin/FormationCrudController.php`

## Changement

```diff
- if (!$this->isGranted('ROLE_FORMATEUR')) {
+ if (!$this->isGranted('ROLE_ADMIN')) {
```

Commentaire mis à jour en cohérence :
- `ROLE_FORMATEUR` (sans ROLE_ADMIN) → révision PENDING
- `ROLE_ADMIN` / `ROLE_SUPER_ADMIN` → révision AUTO_APPROVED

## Contexte

`PageCrudController` utilisait déjà correctement `isGranted('ROLE_ADMIN')`.
`WorksCrudController` conserve `isGranted('ROLE_FORMATEUR')` intentionnellement (les formateurs valident les works des stagiaires).
