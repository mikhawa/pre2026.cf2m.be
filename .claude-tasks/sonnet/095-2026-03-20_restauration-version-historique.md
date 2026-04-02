# Tâche 095 — Restauration d'une version de l'historique

**Modèle** : Sonnet
**Justification** : Nouvelle fonctionnalité métier (service + controllers + templates)

## Fichiers modifiés / créés
- `src/Service/RevisionService.php` — 3 méthodes publiques ajoutées
- `src/Controller/Admin/FormationCrudController.php` — action `restaurerHistoriqueFormation` + `restaurerUrl` dans le builder
- `src/Controller/Admin/PageCrudController.php` — action `restaurerHistoriquePage` + `restaurerUrl`
- `src/Controller/Admin/WorksCrudController.php` — action `restaurerHistoriqueWorks` + `restaurerUrl`
- `templates/admin/formation/historique.html.twig` — bouton "Restaurer"
- `templates/admin/page/historique.html.twig` — bouton "Restaurer"
- `templates/admin/works/historique.html.twig` — bouton "Restaurer"

## Résumé
### RevisionService
3 nouvelles méthodes `restaurerFormationHistory`, `restaurerPageHistory`, `restaurerWorksHistory` :
1. Applique les champs scalaires du snapshot historique à l'entité live
2. Synchronise les relations ManyToMany (responsables pour Formation, users pour Page/Works)
3. Flush
4. Crée une nouvelle entrée d'historique `STATUS_AUTO_APPROVED` via `fromFormation/Page/Works`
5. Persist + flush
6. Retourne la nouvelle entrée

### Controllers
Action `restaurerHistorique*` :
- Vérifie que la version est `STATUS_APPROVED` ou `STATUS_AUTO_APPROVED` (pas PENDING ni REJECTED)
- Appelle `revisionService->restaurerXxxHistory($history, $reviewer)`
- Flash success + redirection vers la page historique

### Templates
Bouton "Restaurer cette version" (jaune/warning) visible uniquement :
- Pour ROLE_ADMIN
- Sur les versions approuvées (status 1 ou 3)
- Uniquement sur les versions NON courantes (`not entry.isCurrent`)
- Confirmation native `confirm()`

## Résultat
✅ Un admin peut restaurer n'importe quelle version approuvée depuis la page historique.
La restauration crée une nouvelle entrée auto-approuvée (traçabilité conservée).
