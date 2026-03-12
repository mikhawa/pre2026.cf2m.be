# 061 — Correction : attribut #[AdminRoute] manquant sur les actions custom de révision

**Date** : 2026-03-12 19:30
**Modèle** : Sonnet

## Fichier modifié
- `src/Controller/Admin/RevisionCrudController.php`

## Problème
Les actions « Approuver », « Rejeter » et « Restaurer » sur les révisions ne fonctionnaient pas. Aucune requête HTTP n'atteignait jamais les méthodes correspondantes.

## Cause
En EasyAdmin 4 avec `#[AdminDashboard]`, la génération des routes custom passe par `AdminRouteGenerator::getCustomActionsConfig()` qui ne traite que les méthodes annotées avec `#[AdminRoute]`. Sans cet attribut, `AdminUrlGenerator::generateUrl()` ne trouve pas de route pour l'action et replie sur la route du dashboard (`/admin`). Le clic redirige vers le tableau de bord sans rien faire.

## Correction
Ajout de l'attribut `#[AdminRoute]` sur les trois méthodes d'action custom :

```php
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;

#[AdminRoute(path: '/{entityId}/approuver', name: 'approuver_revision')]
public function approuverRevision(AdminContext $context): Response { ... }

#[AdminRoute(path: '/{entityId}/rejeter', name: 'rejeter_revision')]
public function rejeterRevision(AdminContext $context): Response { ... }

#[AdminRoute(path: '/{entityId}/restaurer', name: 'restaurer_revision')]
public function restaurerRevision(AdminContext $context): Response { ... }
```

## Routes générées après correction
```
admin_revision_approuver_revision  GET|POST  /admin/revision/{entityId}/approuver
admin_revision_rejeter_revision    GET|POST  /admin/revision/{entityId}/rejeter
admin_revision_restaurer_revision  GET|POST  /admin/revision/{entityId}/restaurer
```
