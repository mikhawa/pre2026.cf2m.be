# 122 — Correction : ROLE_FORMATEUR → révision PENDING pour Formation

**Modèle** : Sonnet
**Justification** : Correction de logique métier dans un controller

## Problème

`FormationCrudController::updateEntity()` utilisait `isGranted('ROLE_FORMATEUR')` comme seuil d'auto-approbation. Or, dans la hiérarchie Symfony, `isGranted('ROLE_FORMATEUR')` retourne `true` pour ROLE_FORMATEUR, ROLE_ADMIN et ROLE_SUPER_ADMIN — ce qui faisait passer les modifications des formateurs directement en AUTO_APPROVED sans révision.

## Fichiers modifiés

- `src/Controller/Admin/FormationCrudController.php` — 4 occurrences corrigées
- `templates/admin/formation/historique.html.twig` — boutons d'action masqués pour ROLE_FORMATEUR

## Changements

**updateEntity()** :
```diff
- if (!$this->isGranted('ROLE_FORMATEUR')) {
+ if (!$this->isGranted('ROLE_ADMIN')) {
```

**approuverHistoriqueFormation(), rejeterHistoriqueFormation(), restaurerHistoriqueFormation()** :
```diff
- $this->denyAccessUnlessGranted('ROLE_FORMATEUR');
+ $this->denyAccessUnlessGranted('ROLE_ADMIN');
```

**historique.html.twig** :
```diff
- {% if is_granted('ROLE_FORMATEUR') %}
+ {% if is_granted('ROLE_ADMIN') %}
```

Comportement après correction :
- `ROLE_FORMATEUR` → révision PENDING (ne peut ni approuver ni rejeter ni restaurer)
- `ROLE_ADMIN` / `ROLE_SUPER_ADMIN` → révision AUTO_APPROVED, peut gérer l'historique

## Contexte

`PageCrudController` utilisait déjà correctement `isGranted('ROLE_ADMIN')`.
`WorksCrudController` conserve `isGranted('ROLE_FORMATEUR')` intentionnellement (les formateurs valident les works des stagiaires).
