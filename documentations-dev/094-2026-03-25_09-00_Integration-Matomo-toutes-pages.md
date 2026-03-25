# 094 — Intégration Matomo sur toutes les pages

**Date** : 2026-03-25 09:00
**Modèle** : Haiku

## Fichiers modifiés/créés
- `templates/_matomo.html.twig` (créé) — partial avec le code de suivi Matomo
- `templates/base.html.twig` — include du partial avant `</body>`
- `templates/bundles/EasyAdminBundle/layout.html.twig` (créé) — override du layout EasyAdmin

## Résumé
Le code de suivi Matomo (`//statistiques.cf2m.be/`, site ID 3) est désormais chargé sur toutes les pages du site, y compris le back-office EasyAdmin.

**Architecture** :
- `templates/_matomo.html.twig` : partial unique, évite la duplication
- Frontend : inclus via `base.html.twig` avant `</body>`
- EasyAdmin : inclus via l'override `templates/bundles/EasyAdminBundle/layout.html.twig` qui étend `@!EasyAdmin/layout.html.twig` et surcharge le block `body_javascript`

## Raison
Demande du client pour avoir des statistiques de fréquentation sur l'ensemble du site, y compris l'interface d'administration.
