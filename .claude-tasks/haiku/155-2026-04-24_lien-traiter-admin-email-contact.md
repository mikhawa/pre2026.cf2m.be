---
modèle: haiku
date: 2026-04-24
justification: Ajout d'un lien dans un template email + injection de service dans un controller
---

# 155 — Lien "TRAITER" vers l'admin dans le mail de notification de contact

## Fonctionnalité
Après la section MESSAGE, le mail de notification de contact contient désormais une section TRAITER avec un bouton lien direct vers la page de détail du message dans EasyAdmin.
L'URL est absolue, générée via AdminUrlGenerator + Request::getUriForPath().

## Fichiers modifiés
- `src/Controller/ContactController.php` — injection AdminUrlGenerator, génération URL absolue après flush, passage dans le contexte email
- `templates/emails/contact.html.twig` — ajout section TRAITER avec bouton lien admin
