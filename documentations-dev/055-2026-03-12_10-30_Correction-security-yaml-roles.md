# 055 — Correction security.yaml : syntaxe YAML et hiérarchie des rôles

**Date** : 2026-03-12 10:30
**Fichier modifié** : `config/packages/security.yaml`

## Problèmes corrigés

### 1. Syntaxe YAML invalide (ligne 36)
`roles: ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_FORMATEUR` → syntaxe invalide en YAML (virgules hors tableau).

### 2. ROLE_FORMATEUR absent de la hiérarchie
Ce rôle était utilisé dans `access_control` mais non déclaré dans `role_hierarchy`.

## Corrections apportées

**Hiérarchie** :
```yaml
ROLE_FORMATEUR:   ROLE_USER
ROLE_ADMIN:       [ROLE_FORMATEUR, ROLE_USER]
ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_FORMATEUR, ROLE_USER]
```

**access_control** :
```yaml
- { path: ^/admin, roles: ROLE_FORMATEUR }
```
(ADMIN et SUPER_ADMIN héritent de FORMATEUR → accès accordé automatiquement)

## Raison
Symfony rejette le YAML malformé au démarrage et `ROLE_FORMATEUR` manquait dans la hiérarchie.
