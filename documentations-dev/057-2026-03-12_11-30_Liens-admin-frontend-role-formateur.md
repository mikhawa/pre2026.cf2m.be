# 057 — Liens admin frontend visibles pour ROLE_FORMATEUR

**Date** : 2026-03-12 11:30
**Fichiers modifiés** :
- `templates/base.html.twig`
- `templates/profil/index.html.twig`

## Résumé
Les liens "Administration" dans le dropdown navbar et sur la page profil utilisaient `is_granted('ROLE_ADMIN')`.
Corrigé en `is_granted('ROLE_FORMATEUR')` pour que les formateurs voient le lien.

ROLE_ADMIN et ROLE_SUPER_ADMIN héritent de ROLE_FORMATEUR → pas de régression.

## Raison
ROLE_FORMATEUR a accès à /admin depuis la correction du DashboardController.
