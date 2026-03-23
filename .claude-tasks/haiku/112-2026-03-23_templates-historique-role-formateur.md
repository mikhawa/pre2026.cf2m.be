# 112 — Templates historique Formation et Works : boutons visibles pour ROLE_FORMATEUR

**Modèle** : Haiku
**Justification** : Correction de garde Twig simple.

## Fichiers modifiés
- `templates/admin/formation/historique.html.twig`
- `templates/admin/works/historique.html.twig`

## Résumé

Les boutons Approuver / Rejeter / Restaurer étaient masqués par `{% if is_granted('ROLE_ADMIN') %}`
dans les deux templates, alors que les controllers avaient déjà été ouverts à `ROLE_FORMATEUR`.

Correction : `ROLE_ADMIN` → `ROLE_FORMATEUR` dans les deux templates.
