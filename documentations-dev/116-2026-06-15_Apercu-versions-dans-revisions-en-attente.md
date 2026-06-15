# 116 — Aperçu des versions dans la page "Révisions en attente"

**Date :** 2026-06-15  
**Commit :** `1b75db5`  
**Branche :** `feature/24-prepa-for-design`

---

## Contexte

La page centralisée `/admin/revisions-en-attente` permettait d'approuver ou rejeter une révision, et d'accéder à l'historique complet. Mais il n'était pas possible de **prévisualiser le rendu réel** de la version en attente sans quitter la page.

Les pages d'historique individuelles (Formation, Page, Works) disposaient déjà d'un bouton "Prévisualiser v{{ n }}" via le `HistoryPreviewController` (voir doc #096).

---

## Fonctionnalité ajoutée

Un bouton **"Prévisualiser vX"** a été ajouté dans le footer de chaque carte de révision en attente. Il ouvre le template de prévisualisation dans un nouvel onglet (`target="_blank"`), identique à ce qui existe sur les pages d'historique.

**Visuel du footer après modification :**

```
[ ✓ Approuver & appliquer ]  [ ✗ Rejeter ]  [ 👁 Prévisualiser v3 ]  … [ ↩ Historique complet ]
```

---

## Implémentation

### `src/Controller/Admin/RevisionsPendantesController.php`

Ajout de `previewUrl` dans chaque tableau `$entries`, en utilisant les routes existantes du `HistoryPreviewController` :

| Type | Route utilisée |
|------|---------------|
| Formation | `admin_preview_history_formation` |
| Page | `admin_preview_history_page` |
| Works | `admin_preview_history_works` |

```php
'previewUrl' => $this->generateUrl('admin_preview_history_formation', ['id' => $pending->getId()]),
```

L'`id` passé est celui de l'entrée d'historique (ex. `FormationHistory`), pas de l'entité parente.

### `templates/admin/revisions-en-attente.html.twig`

```twig
<a href="{{ entry.previewUrl }}"
   target="_blank"
   class="btn btn-sm btn-outline-info">
    <i class="fa fa-eye me-1"></i> Prévisualiser v{{ entry.revision.version }}
</a>
```

---

## Fichiers modifiés

- `src/Controller/Admin/RevisionsPendantesController.php`
- `templates/admin/revisions-en-attente.html.twig`
