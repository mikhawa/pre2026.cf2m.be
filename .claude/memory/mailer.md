---
name: mailer
description: Mailpit en dev vs Mailjet en préprod/prod — configuration DSN et garde-fous
metadata:
  type: reference
---

## Mailer : Mailpit (dev) vs Mailjet (preprod/*)
- `symfony/mailjet-mailer 7.4.*` est installé dans `composer.json` — ne jamais le retirer
- `config/packages/mailer.yaml` lit `%env(MAILER_DSN)%` — aucun changement de code nécessaire
- Dev local : `MAILER_DSN=smtp://mailpit:1025` (défaut dans `.env`)
- Préprod/prod : `MAILER_DSN=mailjet+api://...` dans `.env.local` sur le serveur (géré manuellement, jamais par Claude)
- Sur les branches `preprod/*` : vérifier que `symfony/mailjet-mailer` est bien dans `composer.json`, sinon l'ajouter
- Ne jamais créer ni modifier `.env.local`, ni toucher aux fichiers `.git/`
