# 113 — Lien Administration navbar visible pour ROLE_STAGIAIRE

**Modèle** : Haiku
**Justification** : Correction d'une garde Twig simple.

## Fichiers modifiés
- `templates/base.html.twig`

## Résumé

Le lien "Administration" dans le dropdown utilisateur était conditionné à `ROLE_FORMATEUR`.
Les stagiaires (`ROLE_STAGIAIRE`) ne voyaient donc jamais le lien, même si `/admin` leur est accessible.

Correction : `{% if is_granted('ROLE_FORMATEUR') %}` → `{% if is_granted('ROLE_STAGIAIRE') %}`
