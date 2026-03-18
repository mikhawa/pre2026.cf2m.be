---
modèle: haiku
justification: Modification simple de champ et surcharge de méthode EasyAdmin
date: 2026-03-18
---

# 080 — Création Formation : createdBy assigné automatiquement

## Fichier modifié
- `src/Controller/Admin/FormationCrudController.php`

## Résumé
Lors de la création d'une Formation, le champ "Créée par" est masqué et
l'utilisateur courant est automatiquement assigné comme créateur.

## Changements
- `configureFields` : ajout de `->hideWhenCreating()` sur `AssociationField` `createdBy`
- Ajout de `persistEntity()` : assigne `$this->getUser()` si `createdBy` est null

## Résultat
L'administrateur ne voit pas le champ "Créée par" à la création ;
la formation lui est automatiquement attribuée.
