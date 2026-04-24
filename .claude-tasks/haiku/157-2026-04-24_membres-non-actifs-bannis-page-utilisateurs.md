---
modèle: haiku
date: 2026-04-24
justification: Changement de méthode repository + badge template, modification minime
---

# 157 — Affichage de tous les membres sur /profil/utilisateurs

## Comportement
La page /profil/utilisateurs affiche désormais tous les membres (status 0, 1 et 2).
Les membres non activés portent un badge gris "Non activé", les bannis un badge rouge "Banni".

## Fichiers modifiés
- `src/Controller/ProfileController.php` — utilisation de `findAllOrderedByName()` au lieu de `findAllActiveOrderedByName()`
- `templates/profil/utilisateurs.html.twig` — ajout des badges de statut pour status 0 et 2
