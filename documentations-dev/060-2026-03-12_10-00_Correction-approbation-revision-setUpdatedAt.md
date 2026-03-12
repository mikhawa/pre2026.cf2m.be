# 060 — Correction approbation révision : setUpdatedAt manquant

**Date** : 2026-03-12
**Fichier modifié** : `src/Service/RevisionService.php`

## Résumé
La méthode `applyFormation()` du `RevisionService` n'appelait pas `setUpdatedAt()` après avoir appliqué les champs du snapshot sur l'entité Formation. Doctrine ne détectait donc pas de changement dans l'Unit of Work et n'incluait pas l'entité dans le `flush()`, ce qui faisait que les modifications n'étaient jamais écrites en base.

## Changement
Ajout de `$entity->setUpdatedAt(new \DateTimeImmutable())` à la fin de `applyFormation()`.

## Raison
L'entité `Formation` possède un champ `updatedAt` (nullable). Son absence dans `applyFormation()` laissait Doctrine indécis sur la "dirtiness" de l'entité dans certains cas. Forcer `updatedAt` garantit que l'entité est marquée comme modifiée.
