---
modèle: haiku
date: 2026-05-05
justification: Modification d'une seule condition de permission — changement minimal
---

## Tâche

Donner à `ROLE_PEDAGO` un accès complet aux préviews des versions de formations (`/admin/preview/formation-history/{id}`), sans restriction par responsable.

## Contexte

`ROLE_PEDAGO` hérite déjà de `ROLE_FORMATEUR` → passe le `denyAccessUnlessGranted`.
Le second filtre `if (!isGranted('ROLE_ADMIN'))` le restreignait aux formations dont il est responsable.

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/Admin/HistoryPreviewController.php` | Condition ligne 38 : ajout `&& !$this->isGranted('ROLE_PEDAGO')` |

## Permissions résultantes

| Rôle | Accès |
|---|---|
| `ROLE_SUPER_ADMIN`, `ROLE_ADMIN` | Toutes les formations |
| `ROLE_PEDAGO` | Toutes les formations |
| `ROLE_FORMATEUR` | Formations dont il est responsable uniquement |
| Autres | Refusé |
