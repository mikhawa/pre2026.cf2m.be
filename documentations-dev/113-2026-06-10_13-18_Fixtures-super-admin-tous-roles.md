# 113 — Fixtures : super admin avec tous les rôles

**Date :** 2026-06-10 13:18  
**Commits :** `58e9fac` (AppFixtures) · `3b74ab3` (ProdFixtures)  
**Branche :** `feature/24-prepa-for-design`

---

## Contexte

L'utilisateur super admin créé par les fixtures (dev et prod) n'avait que le rôle `ROLE_SUPER_ADMIN`. Pour faciliter les tests de toutes les fonctionnalités en un seul compte, il est désormais créé avec l'ensemble des rôles.

## Changement

**Avant :**
```php
'roles' => ['ROLE_SUPER_ADMIN']
```

**Après :**
```php
'roles' => ["ROLE_SUPER_ADMIN","ROLE_USER","ROLE_ADMIN","ROLE_PEDAGO","ROLE_FORMATEUR","ROLE_STAGIAIRE"]
```

## Fichiers modifiés

- `src/DataFixtures/AppFixtures.php`
- `src/DataFixtures/ProdFixtures.php`

## Raison

Permet de tester toutes les vues et permissions (admin, pédago, formateur, stagiaire) avec un unique compte en environnement de développement et de préproduction, sans avoir à basculer entre plusieurs comptes.
