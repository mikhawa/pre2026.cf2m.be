# 088 — Ajout description_courte et logo sur Formation

**Date** : 2026-03-21 10:00
**Branche** : feature/structure-for-database

## Fichiers modifiés
- `config/packages/vich_uploader.yaml`
- `src/Entity/Formation.php`
- `src/Entity/FormationHistory.php`
- `src/Service/RevisionService.php`
- `src/Factory/FormationFactory.php`
- `tests/Entity/FormationTest.php`
- `templates/home/index.html.twig`
- `src/Controller/Admin/FormationCrudController.php`
- `migrations/Version20260321100000.php` (à exécuter)
- `public/uploads/formation-logos/.gitignore`

## Changements

### Entité Formation
- Nouveau champ `descriptionCourte` : `VARCHAR(800) NULL`, avec `Assert\Length(max: 800)`
- Nouveau champ `logo` : `VARCHAR(255) NULL`, géré par VichUploader
- Nouveau champ `logoFile` : non mappé, `Vich\UploadableField(mapping: 'formation_logo')`
- Classe annotée `#[Vich\Uploadable]`
- `setLogoFile()` met à jour `updatedAt` pour déclencher Vich

### Entité FormationHistory (snapshot)
- Mêmes champs `descriptionCourte` et `logo` (string) ajoutés
- `fromFormation()` copie les deux nouveaux champs

### RevisionService
- `snapshotFormation()`, `applyRevisionDataToFormation()`, `applyFormation()`, `snapshotFromFormationHistory()`, `buildTypedHistoryDiffHtml()` : tous mis à jour avec les nouveaux champs

### VichUploader
- Nouveau mapping `formation_logo` → `public/uploads/formation-logos/`

### EasyAdmin
- `TextareaField` pour `descriptionCourte` (avec aide contextuelle)
- `Field + VichImageType` pour l'upload de logo
- `ImageField` pour l'affichage du logo (hors formulaire)

### Template accueil
- Affiche `descriptionCourte` si renseigné, sinon fallback `description|plain_text` tronqué à 150 cars

### Migration
Exécuter : `php bin/console doctrine:migrations:migrate`

## Raison
Besoin d'une description courte dédiée à l'affichage sur la page d'accueil (max 800 chars, sans HTML), et d'un logo propre à chaque formation.
