# 181 — Ajout du lien Prévisualiser dans les révisions en attente

**Modèle** : Haiku  
**Justification** : Ajout d'un bouton simple vers des routes existantes — aucune logique métier.

## Fonctionnalité

La page "Révisions en attente" affiche maintenant un bouton **Prévisualiser** (ouvre un nouvel onglet) pour chaque révision, identique à ce qui existe dans les pages d'historique individuelles (Works, Formations, Pages).

## Implémentation

- `RevisionsPendantesController.php` : ajout de `previewUrl` dans chaque entrée du tableau `$entries`, via `$this->generateUrl()` vers les routes `admin_preview_history_formation`, `admin_preview_history_page`, `admin_preview_history_works` avec l'ID de la révision en attente.
- `templates/admin/revisions-en-attente.html.twig` : ajout du bouton `btn-outline-info` entre "Rejeter" et "Historique complet".

## Fichiers modifiés

- `src/Controller/Admin/RevisionsPendantesController.php`
- `templates/admin/revisions-en-attente.html.twig`
