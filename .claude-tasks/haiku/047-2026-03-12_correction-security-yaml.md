---
modèle: haiku
justification: Correction de syntaxe YAML et hiérarchie des rôles
fichiers modifiés:
  - config/packages/security.yaml
---

## Résumé
- Correction syntaxe YAML ligne 36 : `roles: ROLE_X, ROLE_Y, ROLE_Z` → un seul rôle grâce à la hiérarchie
- Ajout de `ROLE_FORMATEUR` dans la hiérarchie (manquant)
- `access_control ^/admin` simplifié à `ROLE_FORMATEUR` (grâce à l'héritage : ADMIN ⊃ FORMATEUR, SUPER_ADMIN ⊃ ADMIN)

## Hiérarchie finale
- ROLE_FORMATEUR → ROLE_USER
- ROLE_ADMIN → ROLE_FORMATEUR, ROLE_USER
- ROLE_SUPER_ADMIN → ROLE_ADMIN, ROLE_FORMATEUR, ROLE_USER

## Résultat
YAML valide, hiérarchie cohérente avec les 4 rôles définis dans CONTEXT.md.
