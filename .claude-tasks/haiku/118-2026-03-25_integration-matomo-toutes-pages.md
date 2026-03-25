# 118 — Intégration Matomo sur toutes les pages (frontend + EasyAdmin)

**Modèle** : Haiku
**Justification** : Ajout simple de templates Twig, aucune logique métier

## Fichiers modifiés/créés
- `templates/_matomo.html.twig` — partial Matomo réutilisable (créé)
- `templates/base.html.twig` — include du partial avant `</body>`
- `templates/bundles/EasyAdminBundle/layout.html.twig` — override EasyAdmin, inject Matomo dans `body_javascript` (créé)

## Résumé
Code de suivi Matomo (`statistiques.cf2m.be`, site ID 3) présent sur toutes les pages :
- Frontend : via `base.html.twig`
- Back-office EasyAdmin : via override `@!EasyAdmin/layout.html.twig`
Le partial `_matomo.html.twig` évite la duplication.

## Résultat
Matomo chargé sur toutes les pages publiques et d'administration.
