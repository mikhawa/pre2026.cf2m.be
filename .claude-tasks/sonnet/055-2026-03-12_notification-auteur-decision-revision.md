# 055 — Notification email à l'auteur lors de la décision sur une révision

**Modèle** : Sonnet
**Justification** : Ajout d'une fonctionnalité de notification dans un service métier existant

## Fichiers modifiés / créés
- `src/Service/RevisionService.php` — ajout de la méthode `notifyAuthor(Revision, bool)`
- `src/Controller/Admin/RevisionCrudController.php` — appel de `notifyAuthor()` dans `approuverRevision()` et `rejeterRevision()`
- `templates/emails/revision_decision.html.twig` — nouveau template email (approbation / rejet)

## Résumé
Lorsqu'un administrateur approuve ou rejette une révision, un email est envoyé à l'auteur de la révision (`revision.createdBy`) depuis `MAIL_FORM`.

Le template affiche :
- Un bandeau coloré (vert = approuvé, rouge = rejeté)
- Le type et le titre de l'entité concernée
- Le nom du validateur
- La note de révision si présente
- Les dates de soumission et de décision
