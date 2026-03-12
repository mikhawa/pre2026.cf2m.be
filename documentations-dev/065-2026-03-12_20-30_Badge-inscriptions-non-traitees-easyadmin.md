# 065 — Badge inscriptions non traitées dans la sidebar EasyAdmin

**Date** : 2026-03-12 20:30
**Modèle** : Haiku

## Fichiers modifiés
- `src/Repository/InscriptionRepository.php`
- `src/Controller/Admin/DashboardController.php`

## Changements
Ajout de `InscriptionRepository::findUntreatedCount()` (count WHERE treat = false) et badge rouge `danger` sur le menu "Inscriptions" dans la sidebar EasyAdmin, affiché uniquement si count > 0.
