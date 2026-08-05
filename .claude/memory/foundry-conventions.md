---
name: foundry-conventions
description: Conventions Foundry 2.x pour les factories (createMany/createOne, PropertyAccessor sur les booleans is*)
metadata:
  type: project
---

## Conventions Foundry 2.x (factories)
- `createMany()` / `createOne()` retournent des entités directement (pas de proxies, pas de `_real()`)
- Pour les booleans `is*` : utiliser la clé sans préfixe `is` pour PropertyAccessor
  - `isActive` → clé `'active'` (setter `setActive()`)
  - `isApproved` → clé `'approved'` (setter `setApproved()`)
  - `isRead` → clé `'read'` (setter `setRead()`)
- Defaults callable (`fn():array`) pour générer slug depuis le titre
- Hachage MDP dans `afterInstantiate` via injection de `UserPasswordHasherInterface`
