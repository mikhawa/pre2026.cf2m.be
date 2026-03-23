# 093 — Correction email invalide RFC 2822 dans UserFactory

**Date** : 2026-03-23 15:00
**Fichier modifié** : `src/Factory/UserFactory.php`

## Problème

Erreur lors de l'envoi d'email depuis `/admin/formation/251/edit` avec un rôle ROLE_FORMATEUR :

```
Email "Femke-Van Dyck@cf2m.be" does not comply with addr-spec of RFC 2822.
```

`faker()->lastName()` retourne parfois des noms composés avec espace (`Van Dyck`).
La concaténation `$userName.'@cf2m.be'` produisait un email invalide contenant un espace.

## Correction

```php
// Avant
$email = $userName.'@'.'cf2m.be';

// Après
$email = str_replace(' ', '-', $userName).'@cf2m.be';
```

Les espaces dans le nom (ex. `Van Dyck`) sont remplacés par des tirets,
donnant `Femke-Van-Dyck@cf2m.be`, conforme RFC 2822.

## Impact

Affecte uniquement les fixtures (données de développement).
Les utilisateurs réels saisissent leur propre email dans un champ validé.
