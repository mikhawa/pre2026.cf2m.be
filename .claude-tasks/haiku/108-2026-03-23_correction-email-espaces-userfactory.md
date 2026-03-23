# 108 — Correction email invalide RFC 2822 dans UserFactory

**Modèle** : Haiku
**Justification** : Correction simple d'une ligne dans une factory.

## Fichiers modifiés
- `src/Factory/UserFactory.php`

## Résumé

`faker()->lastName()` peut retourner des noms composés avec espace (ex. `Van Dyck`),
générant un email invalide (`Femke-Van Dyck@cf2m.be`) non conforme à la RFC 2822.

### Correction
Remplacement des espaces par des tirets dans la partie locale de l'email :
```php
// Avant
$email = $userName.'@'.'cf2m.be';
// Après
$email = str_replace(' ', '-', $userName).'@cf2m.be';
```

## Résultat
Les emails générés par les fixtures respectent désormais la RFC 2822.
L'erreur `Email does not comply with addr-spec of RFC 2822` est corrigée.
