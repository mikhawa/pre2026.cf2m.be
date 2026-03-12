# 052 — Correction approbation révision : setUpdatedAt manquant

**Modèle** : Sonnet
**Justification** : Correction d'un bug dans un service métier

## Problème
Lors de l'approbation d'une révision, les modifications n'étaient pas appliquées à l'entité Formation en base de données. Doctrine ne détectait pas l'entité comme "dirty" car aucun champ réellement suivi par le change-tracker n'était modifié de façon certaine.

## Fichier modifié
- `src/Service/RevisionService.php` — méthode `applyFormation()` : ajout de `$entity->setUpdatedAt(new \DateTimeImmutable())` pour forcer la détection du changement par l'Unit of Work Doctrine.

## Résumé
Ajout de `$entity->setUpdatedAt(new \DateTimeImmutable())` à la fin de `applyFormation()` afin que Doctrine marque l'entité Formation comme modifiée et l'inclue dans le `flush()`.

## Résultat attendu
L'approbation d'une révision Formation applique désormais correctement les champs du snapshot (title, slug, description, status, publishedAt, colorPrimary, colorSecondary) à l'entité live.
