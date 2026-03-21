# 082 — Correction lien /admin/revision sur la page profil

**Date** : 2026-03-20 21:15
**Tâche** : 092

## Fichier modifié
- `templates/profil/index.html.twig`

## Résumé
La bannière d'alerte "N révisions en attente" sur la page profil admin pointait vers `/admin/revision` (hardcodé), une route supprimée lors de la Phase 5 de la refactorisation des tables d'historique typées.

### Correctif
Remplacement de `href="/admin/revision"` par `href="{{ path('admin') }}"` → redirige vers le tableau de bord EasyAdmin, où les badges de révisions par type de contenu sont visibles.

## Raison
Page 404 lors du clic sur la bannière d'alerte depuis la page profil.
