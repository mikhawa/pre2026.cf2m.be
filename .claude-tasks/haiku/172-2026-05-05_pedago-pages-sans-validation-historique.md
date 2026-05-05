---
modèle: haiku
date: 2026-05-05
justification: Correction de deux conditions de permission — changements ciblés
---

## Tâche

Donner à `ROLE_PEDAGO` les mêmes droits que `ROLE_ADMIN` sur les pages :
- Écriture/modification sans validation (auto-approved)
- Accès aux préviews de l'historique des pages

## Corrections

### 1. `PageCrudController::updateEntity()`
`$isAdmin = $this->isGranted('ROLE_ADMIN')` → `$isAutoApproved = $this->isGranted('CONTENT_MANAGER')`

`CONTENT_MANAGER` est accordé à `ROLE_ADMIN` et `ROLE_PEDAGO` (via `ContentManagerVoter`).
Les PEDAGO créaient des révisions PENDING au lieu d'auto-approved.

### 2. `HistoryPreviewController::previewPage()`
`denyAccessUnlessGranted('ROLE_ADMIN')` → `denyAccessUnlessGranted('CONTENT_MANAGER')`

`ROLE_PEDAGO` n'hérite pas de `ROLE_ADMIN`, donc le preview `/admin/preview/page-history/{id}` était bloqué en 403.

## Permissions résultantes sur les pages

| Rôle | Modifier page | Preview historique |
|---|---|---|
| `ROLE_SUPER_ADMIN` / `ROLE_ADMIN` | Auto-approved | ✅ |
| `ROLE_PEDAGO` | Auto-approved | ✅ |
| `ROLE_FORMATEUR` | PENDING (validation requise) | ❌ |
| Autres | ❌ | ❌ |

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/Admin/PageCrudController.php` | `updateEntity()` : `isGranted('ROLE_ADMIN')` → `isGranted('CONTENT_MANAGER')` |
| `src/Controller/Admin/HistoryPreviewController.php` | `previewPage()` : `denyAccessUnlessGranted('ROLE_ADMIN')` → `'CONTENT_MANAGER'` |
