# 011 — Création de UserRepository

**Date** : 2026-03-03
**Fichier créé** : `src/Repository/UserRepository.php`

## Contenu
- Étend `ServiceEntityRepository<User>`
- Implémente `PasswordUpgraderInterface` (rehashage automatique du mot de passe)
- Méthode `upgradePassword()` : met à jour le hash si l'algorithme évolue
- PHPDoc `@extends` pour le typage générique du repository
