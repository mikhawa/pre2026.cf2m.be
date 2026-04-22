# 148 — Correction des dépréciations Symfony Security

**Modèle** : Haiku
**Justification** : Corrections syntaxiques pures, aucune logique métier modifiée.

## Fichiers modifiés

- `src/Security/UserChecker.php`
- `src/Security/Voter/ContentManagerVoter.php`
- `src/Security/Voter/FormationVoter.php`
- `src/Security/Voter/WorksVoter.php`

## Résumé

Ajout des nouveaux paramètres requis par Symfony 8 (préparés en 7.x) :

- `checkPostAuth()` → `?TokenInterface $token = null`
- `voteOnAttribute()` (3 voters) → `?Vote $vote = null`

Import de `Vote` (`Symfony\Component\Security\Core\Authorization\Voter\Vote`) ajouté dans les 3 voters.
Import de `TokenInterface` ajouté dans `UserChecker`.
