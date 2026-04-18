---
modèle: haiku
justification: Modification d'une requête repository — retrait d'un rôle des destinataires
fichiers modifiés:
  - src/Repository/UserRepository.php
---

## Résumé

Les emails du formulaire de contact ne sont plus envoyés aux `ROLE_ADMIN`. Seuls `ROLE_SUPER_ADMIN` et `ROLE_PEDAGO` les reçoivent, cohérent avec la même règle appliquée aux préinscriptions (tâche 138).

## Changements

- `findContactRecipients()` : suppression de la clause `WHERE u.roles LIKE '%ROLE_ADMIN%'`
