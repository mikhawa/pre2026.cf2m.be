# 062 — Email expéditeur des notifications de révision depuis MAIL_FORM

**Date** : 2026-03-12 19:45
**Modèle** : Sonnet

## Fichier modifié
- `src/Service/RevisionService.php`

## Changement
L'adresse expéditeur des emails de notification de révision en attente était codée en dur : `noreply@cf2m.be`. Elle utilise désormais la variable d'environnement `MAIL_FORM` (comme le `ContactController`).

## Raison
Utiliser une adresse hardcodée en production et en dev est une mauvaise pratique. La variable `MAIL_FORM` du `.env` centralise la configuration de l'expéditeur.

## Implémentation
```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

public function __construct(
    // ...
    #[Autowire(env: 'MAIL_FORM')]
    private readonly string $mailFrom,
) {}

// Dans notifyAdmins() :
$sender = $this->mailFrom;
```
