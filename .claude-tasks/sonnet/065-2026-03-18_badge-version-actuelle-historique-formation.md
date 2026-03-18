# 065 — Badge "Version actuelle" dans l'historique Formation

**Modèle** : Sonnet
**Justification** : Modification d'un service métier + contrôleur + template Twig

## Fichiers modifiés
- `src/Service/RevisionService.php` — ajout méthode publique `getLiveFormationSnapshot()`
- `src/Controller/Admin/FormationCrudController.php` — calcul snapshot live, flag `isCurrent` par entrée, suppression de `appliquerUrl` pour la version actuelle
- `templates/admin/formation/historique.html.twig` — affichage badge "Version actuelle" (primary) à côté du badge de statut

## Résumé
La version correspondant aux données live de la formation est maintenant identifiée par comparaison de snapshot. Elle affiche un badge bleu "Version actuelle" et n'a plus de bouton "Restaurer cette version" (inutile de restaurer ce qui est déjà en place).

## Résultat
La version live est clairement identifiée dans la timeline. Les autres versions APPROVED conservent leur bouton de restauration.
