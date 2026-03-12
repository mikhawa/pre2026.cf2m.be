# 054 — Mail expéditeur révisions depuis MAIL_FORM (.env)

**Modèle** : Sonnet
**Justification** : Correction simple d'un service métier (injection de paramètre env)

## Problème
Les emails de notification de révision étaient envoyés depuis `noreply@cf2m.be` codé en dur au lieu d'utiliser la variable d'environnement `MAIL_FORM`.

## Fichier modifié
- `src/Service/RevisionService.php`
  - Import de `Symfony\Component\DependencyInjection\Attribute\Autowire`
  - Injection de `#[Autowire(env: 'MAIL_FORM')] string $mailFrom` dans le constructeur
  - Remplacement de `'noreply@cf2m.be'` par `$this->mailFrom`

## Résultat
L'expéditeur des emails de révision correspond désormais à la valeur de `MAIL_FORM` dans `.env` (`testform@cf2m.be` en dev).
