# 063 — Email de notification à l'auteur lors de la décision sur une révision

**Date** : 2026-03-12 20:00
**Modèle** : Sonnet

## Fichiers modifiés / créés
- `src/Service/RevisionService.php`
- `src/Controller/Admin/RevisionCrudController.php`
- `templates/emails/revision_decision.html.twig` (nouveau)

## Fonctionnalité ajoutée
Quand un administrateur approuve ou rejette une révision, un email est envoyé à l'auteur de la révision pour l'informer du résultat.

## Détails techniques

### RevisionService::notifyAuthor()
```php
public function notifyAuthor(Revision $revision, bool $approved): void
```
- Récupère l'email de `$revision->getCreatedBy()`
- Envoie un email depuis `$this->mailFrom` (variable `MAIL_FORM`)
- Passe `revision` et `approved` au template

### Appels dans RevisionCrudController
- `approuverRevision()` : `$this->revisionService->notifyAuthor($revision, true)`
- `rejeterRevision()` : `$this->revisionService->notifyAuthor($revision, false)`

### Template revision_decision.html.twig
- Bandeau vert (approuvé) ou rouge (rejeté)
- Type + titre de l'entité modifiée
- Nom du validateur
- Note de révision si renseignée
- Dates de soumission et de décision
