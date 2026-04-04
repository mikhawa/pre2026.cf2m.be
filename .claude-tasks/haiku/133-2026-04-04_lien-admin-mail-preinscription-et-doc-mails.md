---
modèle: Haiku
justification: Ajout d'un lien dans un template email + création d'un fichier de documentation
date: 2026-04-04
---

# Tâche 133 — Lien admin dans le mail de préinscription + doc permissions-et-mails

## Fichiers modifiés

- `src/Controller/InscriptionController.php` — génération de l'URL absolue vers la liste admin des inscriptions, passée au template
- `templates/emails/inscription_admin.html.twig` — ajout d'un bouton CTA « Gérer les préinscriptions »

## Fichiers créés

- `docs/architecture/permissions-et-mails.md` — documentation exhaustive de tous les envois de mails : déclencheur, destinataires, template, variables d'env, récap par rôle

## Résumé

Le mail reçu par les ROLE_ADMIN lors d'une préinscription contient maintenant un bouton direct vers la liste des inscriptions dans l'administration. L'URL est absolue (UrlGeneratorInterface::ABSOLUTE_URL) pour fonctionner dans les clients mail.
