# 085 — Restauration d'une version de l'historique

**Date** : 2026-03-20 23:00
**Tâche** : 095

## Fichiers modifiés
- `src/Service/RevisionService.php`
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `templates/admin/formation/historique.html.twig`
- `templates/admin/page/historique.html.twig`
- `templates/admin/works/historique.html.twig`

## Résumé
Implémentation du rollback vers une version historique approuvée.

### RevisionService
- `restaurerFormationHistory(FormationHistory, User): FormationHistory`
- `restaurerPageHistory(PageHistory, User): PageHistory`
- `restaurerWorksHistory(WorksHistory, User): WorksHistory`

Chaque méthode : applique les champs scalaires → synchronise les ManyToMany → flush → crée une nouvelle entrée auto-approuvée → persist + flush.

### Controllers (3)
Route `/{entityId}/historique/restaurer` — vérifie STATUS_APPROVED ou STATUS_AUTO_APPROVED, appelle le service, flash success, retour à la page historique.

### Templates (3)
Bouton "Restaurer cette version" (warning) sur les versions approuvées non-courantes.
Masqué sur : versions PENDING, REJECTED, version actuelle, et pour les non-admins.

## Raison
Demande utilisateur : pouvoir revenir à une ancienne version depuis la page historique.
