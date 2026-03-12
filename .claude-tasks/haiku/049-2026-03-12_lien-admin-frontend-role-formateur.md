---
modèle: haiku
justification: Correction de condition Twig (is_granted)
fichiers modifiés:
  - templates/base.html.twig
  - templates/profil/index.html.twig
---

## Résumé
Remplacement de `is_granted('ROLE_ADMIN')` par `is_granted('ROLE_FORMATEUR')` pour les liens vers /admin.
ROLE_ADMIN et ROLE_SUPER_ADMIN héritent de ROLE_FORMATEUR → les liens restent visibles pour eux.
