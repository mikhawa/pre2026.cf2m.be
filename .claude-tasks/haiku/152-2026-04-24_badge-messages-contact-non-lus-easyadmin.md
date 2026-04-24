---
modèle: haiku
date: 2026-04-24
justification: Ajout simple d'une méthode repository + badge dans le menu EasyAdmin
---

# 152 — Badge messages de contact non lus dans le menu EasyAdmin

## Fonctionnalité
Affichage du nombre de messages de contact non lus (`isRead = false`) sous forme de badge rouge dans le menu gauche EasyAdmin, à côté de "Messages de contact".

## Fichiers modifiés
- `src/Repository/ContactMessageRepository.php` — ajout de `countUnread()`
- `src/Controller/Admin/DashboardController.php` — injection du repository + badge sur l'item de menu
