---
modèle: haiku
justification: Restriction de permission simple, modification d'une constante EasyAdmin
date: 2026-03-18
---

# 079 — Création de Formation autorisée pour ROLE_ADMIN

## Fichier modifié
- `src/Controller/Admin/FormationCrudController.php`

## Résumé
`ROLE_ADMIN` peut désormais créer une Formation (action NEW).
Seule la suppression reste réservée à `ROLE_SUPER_ADMIN`.

## Changement
- `Action::NEW` : `ROLE_SUPER_ADMIN` → `ROLE_ADMIN`

## Résultat
| Action    | Avant              | Après            |
|-----------|--------------------|------------------|
| NEW       | ROLE_SUPER_ADMIN   | ROLE_ADMIN       |
| DELETE    | ROLE_SUPER_ADMIN   | ROLE_SUPER_ADMIN |
