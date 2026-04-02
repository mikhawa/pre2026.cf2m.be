# Tâche 127 — Prévisualisation des versions d'historique (Formation / Page / Works)

**Modèle utilisé :** Sonnet
**Justification :** Controller avec logique de sécurité (voters custom, filtre par rôle) + templates métier avec rendu HTML/images/couleurs.

## Fichiers créés

- `src/Controller/Admin/HistoryPreviewController.php`
  - Routes : `/admin/preview/formation-history/{id}`, `/admin/preview/page-history/{id}`, `/admin/preview/works-history/{id}`
  - Accès formation/works : `ROLE_FORMATEUR` + filtre responsables pour non-admin
  - Accès page : `ROLE_ADMIN` uniquement (cohérent avec la page historique)

- `templates/admin/history_preview/formation.html.twig`
  - Template standalone (pas d'héritage EasyAdmin)
  - Affiche : titre, slug, logo (`/uploads/formation-logos/`), description courte, description HTML (`|raw`), couleurs primaire/secondaire (swatches + CSS var), statut, date publication, responsables, note de révision
  - Bandeau fixe en haut : numéro de version, date, auteur, badge statut, bouton "Fermer"

- `templates/admin/history_preview/page.html.twig`
  - Affiche : titre, slug, contenu HTML (`|raw`), statut, date publication, auteurs, note de révision

- `templates/admin/history_preview/works.html.twig`
  - Affiche : titre, slug, description HTML (`|raw`), statut, date publication, formation parente (avec couleur), auteurs, note de révision

## Fichiers modifiés

- `templates/admin/formation/historique.html.twig`
  - Restructuration du footer : bouton "Prévisualiser v{{ rev.version }}" toujours visible (`target="_blank"`)
  - Boutons approbation/rejet/restauration toujours sous `FORMATION_APPROVE`

- `templates/admin/page/historique.html.twig`
  - Même restructuration, bouton preview visible à tout admin, actions sous `ROLE_ADMIN`

- `templates/admin/works/historique.html.twig`
  - Même restructuration, bouton preview visible, actions sous `WORKS_APPROVE`

## Résumé

Chaque version dans la timeline d'historique dispose désormais d'un bouton "Prévisualiser" qui ouvre une fenêtre `_blank` affichant le snapshot complet de cette version : contenu HTML rendu, logo, couleurs (swatches), métadonnées. La sécurité respecte les mêmes règles que la page historique correspondante.
