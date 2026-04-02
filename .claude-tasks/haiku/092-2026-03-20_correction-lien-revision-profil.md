# Tâche 092 — Correction lien /admin/revision sur la page profil

**Modèle** : Haiku
**Justification** : Correction d'un lien hardcodé (typo/syntaxe)

## Fichier modifié
- `templates/profil/index.html.twig`

## Résumé
Remplacement du lien hardcodé `/admin/revision` (page supprimée en Phase 5) par `{{ path('admin') }}` sur la bannière d'alerte "révisions en attente" de la page profil admin.

## Résultat
✅ Le clic sur la bannière mène désormais au tableau de bord EasyAdmin.
