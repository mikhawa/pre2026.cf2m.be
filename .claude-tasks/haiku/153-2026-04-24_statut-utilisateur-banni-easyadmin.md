---
modèle: haiku
date: 2026-04-24
justification: Modification de champ EasyAdmin uniquement, sans migration BDD
---

# 153 — Statut utilisateur : ajout de l'option "Banni" (2) dans EasyAdmin

## Fonctionnalité
Remplacement du `IntegerField` brut du champ `status` par un `ChoiceField` EasyAdmin avec libellés textuels et badges colorés :
- 0 → "Non activé" (badge gris)
- 1 → "Activé" (badge vert)
- 2 → "Banni" (badge rouge)

Visible sur la liste, dans le formulaire d'édition, et dans les filtres.

## Fichiers modifiés
- `src/Controller/Admin/UserCrudController.php` — remplacement IntegerField → ChoiceField, ajout filtre statut
