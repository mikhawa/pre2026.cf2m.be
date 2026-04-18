---
modèle: haiku
justification: Modification d'une requête repository — retrait d'un rôle des destinataires
fichiers modifiés:
  - src/Repository/UserRepository.php
---

## Résumé

Les emails de préinscription ne sont plus envoyés aux `ROLE_ADMIN`. Seuls `ROLE_SUPER_ADMIN` et `ROLE_PEDAGO` les reçoivent, pour éviter les doublons et limiter les notifications aux personnes directement concernées par la validation des inscriptions.

## Changements

- `findInscriptionRecipients()` : suppression de la clause `WHERE u.roles LIKE '%ROLE_ADMIN%'`
