# 098 — Ajout description_courte et logo sur Formation

**Modèle** : Sonnet
**Justification** : Modification multi-fichiers cohérente (entité + historique + service + factory + tests + template + admin + migration)

## Fichiers modifiés
- `config/packages/vich_uploader.yaml` — mapping `formation_logo`
- `src/Entity/Formation.php` — `descriptionCourte`, `logoFile` (Vich), `logo`
- `src/Entity/FormationHistory.php` — `descriptionCourte`, `logo` (snapshot)
- `src/Service/RevisionService.php` — 5 méthodes mises à jour (snapshotFormation, applyRevisionDataToFormation, applyFormation, snapshotFromFormationHistory, buildTypedHistoryDiffHtml)
- `src/Factory/FormationFactory.php` — `descriptionCourte` dans defaults
- `tests/Entity/FormationTest.php` — testDefaultValues, testSettersReturnStatic, testDescriptionCourte, testLogo
- `templates/home/index.html.twig` — affiche `descriptionCourte` en priorité, fallback sur `description|plain_text`
- `src/Controller/Admin/FormationCrudController.php` — TextareaField descriptionCourte, Field logoFile (VichImageType), ImageField logo (display)
- `migrations/Version20260321100000.php` — ADD COLUMN sur formation et formation_history
- `public/uploads/formation-logos/.gitignore` — dossier de stockage des logos

## Résumé
- `description_courte` VARCHAR(800) nullable, affiché sur l'accueil à la place de `description`
- `logo` VARCHAR(255) nullable, géré par VichUploader (mapping `formation_logo`)
- Le système d'historique est maintenu : `FormationHistory` snapshote les deux nouveaux champs, `RevisionService` les inclut dans tous les snapshots/apply
- Anciens contenus non impactés (colonnes nullable)
