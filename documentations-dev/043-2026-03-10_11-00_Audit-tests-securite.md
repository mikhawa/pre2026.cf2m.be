# 043 — Audit tests & sécurité générale

**Date** : 2026-03-10 11:00
**Fichiers modifiés** :
- `config/packages/security.yaml`
- `src/Entity/ContactMessage.php`
- `composer.json` / `composer.lock`

## Résumé

Audit complet du code existant avec corrections des vulnérabilités identifiées.

### Tests unitaires : 88/88 ✅
Zéro régression.

### `composer audit` : aucune vulnérabilité ✅

### Correctif 1 — Protection brute-force sur la connexion
Installation de `symfony/rate-limiter` et configuration du `login_throttling` dans `security.yaml` :

```yaml
login_throttling:
    max_attempts: 5
    interval: '5 minutes'
```

Limite à 5 tentatives de connexion par tranche de 5 minutes (par IP + email).

### Correctif 2 — Contraintes de longueur sur le formulaire de contact
Les champs de `ContactMessage` n'avaient que des contraintes ORM (niveau DB), sans validation Symfony. Ajout de `Assert\Length` :
- `nom` : max 100
- `email` : max 180
- `sujet` : max 255
- `message` : max 3000 (champ TEXT sans limite DB)

### Points sains confirmés
- CSRF, rôles, hachage MDP, honeypot, QueryBuilder, `strict_types`
