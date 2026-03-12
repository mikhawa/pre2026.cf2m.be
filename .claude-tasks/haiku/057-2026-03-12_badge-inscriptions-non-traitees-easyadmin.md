# 057 — Badge inscriptions non traitées dans EasyAdmin

**Modèle** : Haiku
**Justification** : Ajout simple d'une méthode repository + badge menu

## Fichiers modifiés
- `src/Repository/InscriptionRepository.php` — ajout de `findUntreatedCount()`
- `src/Controller/Admin/DashboardController.php` — injection `InscriptionRepository`, badge `danger` sur le menu "Inscriptions"

## Résumé
Badge rouge sur le menu "Inscriptions" de la sidebar EasyAdmin affichant le nombre d'inscriptions non traitées (`treat = false`). Disparaît si count = 0.
