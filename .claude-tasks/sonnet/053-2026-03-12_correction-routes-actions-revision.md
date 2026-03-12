# 053 — Correction : routes manquantes pour les actions custom de révision

**Modèle** : Sonnet
**Justification** : Débogage d'un bug de routage EasyAdmin 4

## Problème
Les boutons « Approuver », « Rejeter » et « Restaurer » dans l'interface des révisions ne fonctionnaient pas : cliquer dessus ne déclenchait jamais l'action correspondante.

## Cause racine
En EasyAdmin 4 avec `#[AdminDashboard]`, les méthodes d'action custom d'un `AbstractCrudController` **doivent** être annotées avec `#[AdminRoute]` pour que EasyAdmin génère les routes Symfony correspondantes. Sans cet attribut, `AdminRouteGenerator::getCustomActionsConfig()` ignore la méthode, et `AdminUrlGenerator::generateUrl()` n'ayant aucune route pour l'action, replie sur la route du dashboard (`/admin`).

## Fichier modifié
- `src/Controller/Admin/RevisionCrudController.php`
  - Import de `EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute`
  - Ajout de `#[AdminRoute(path: '/{entityId}/approuver', name: 'approuver_revision')]` sur `approuverRevision()`
  - Ajout de `#[AdminRoute(path: '/{entityId}/rejeter', name: 'rejeter_revision')]` sur `rejeterRevision()`
  - Ajout de `#[AdminRoute(path: '/{entityId}/restaurer', name: 'restaurer_revision')]` sur `restaurerRevision()`

## Résultat
Trois nouvelles routes Symfony enregistrées :
- `admin_revision_approuver_revision` → GET|POST `/admin/revision/{entityId}/approuver`
- `admin_revision_rejeter_revision`   → GET|POST `/admin/revision/{entityId}/rejeter`
- `admin_revision_restaurer_revision` → GET|POST `/admin/revision/{entityId}/restaurer`
