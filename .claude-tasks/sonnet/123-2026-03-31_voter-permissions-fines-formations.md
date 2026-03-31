# 123 — Voters Symfony : permissions fines par Formation (Approche 1)

**Date** : 2026-03-31
**Modèle** : Sonnet
**Branche** : feature/05-prepare-real-content-on-fixtures

## Justification du modèle

Implémentation de pattern Symfony (Voters), modifications dans plusieurs couches (Voter, Controller, Service, Twig). Complexité métier modérée → Sonnet.

## Fichiers créés

- `src/Security/Voter/FormationVoter.php` — Voter avec attributs `FORMATION_EDIT_AUTOAPPROVE`, `FORMATION_APPROVE`, `FORMATION_REJECT`, `FORMATION_RESTORE`
- `src/Security/Voter/WorksVoter.php` — Voter avec attributs `WORKS_EDIT_AUTOAPPROVE`, `WORKS_APPROVE`, `WORKS_REJECT`, `WORKS_RESTORE` (traverse vers la Formation parente)

## Fichiers modifiés

- `src/Controller/Admin/FormationCrudController.php`
  - `updateEntity()` : `isGranted('ROLE_ADMIN')` → `isGranted('FORMATION_EDIT_AUTOAPPROVE', $entityInstance)`
  - `approuverHistoriqueFormation()` : `denyAccessUnlessGranted('ROLE_ADMIN')` → `denyAccessUnlessGranted('FORMATION_APPROVE', $formation)`
  - `rejeterHistoriqueFormation()` : idem → `FORMATION_REJECT`
  - `restaurerHistoriqueFormation()` : idem → `FORMATION_RESTORE`

- `src/Controller/Admin/WorksCrudController.php`
  - `updateEntity()` : `isGranted('ROLE_FORMATEUR')` → `isGranted('WORKS_EDIT_AUTOAPPROVE', $entityInstance)`, restructuration du chemin PENDING pour préserver la vérification d'appartenance stagiaire uniquement
  - `approuverHistoriqueWorks()` : `denyAccessUnlessGranted('ROLE_FORMATEUR')` → `denyAccessUnlessGranted('WORKS_APPROVE', $works)`
  - `rejeterHistoriqueWorks()` : idem → `WORKS_REJECT`
  - `restaurerHistoriqueWorks()` : idem → `WORKS_RESTORE`
  - `updateEntity()` appel `notifyFormateurs($revision, $entityInstance)` (ajout du 2e argument)

- `src/Service/RevisionService.php`
  - `notifyFormateurs(Revision $revision)` → `notifyFormateurs(Revision $revision, Works $works)` : notifications ciblées sur les responsables de la Formation parente du Works

- `templates/admin/formation/historique.html.twig`
  - `is_granted('ROLE_ADMIN')` → `is_granted('FORMATION_APPROVE', formation)`

- `templates/admin/works/historique.html.twig`
  - `is_granted('ROLE_FORMATEUR')` → `is_granted('WORKS_APPROVE', works)`

## Résumé

Implémentation de l'Approche 1 du fichier `docs/architecture/permissions-fines-formations.md`.

Logique de décision dans les voters :
- `ACCESS_GRANTED` si `ROLE_ADMIN` (hiérarchie complète)
- OU `ROLE_FORMATEUR` ET l'utilisateur est dans `$formation->getResponsables()`
- `ACCESS_DENIED` sinon

Un formateur responsable d'une formation peut désormais :
- Auto-approuver ses modifications sur CETTE formation (et ses Works)
- Approuver/rejeter/restaurer les révisions en attente sur CETTE formation

Un formateur non-responsable reste en PENDING comme avant.
Aucune migration BDD nécessaire (relation `formation_user` déjà existante).
