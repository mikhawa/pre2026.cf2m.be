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
- `src/Controller/ContactController.php` — génération URL absolue via route EasyAdmin `admin_contact_message_detail`, passage dans le contexte email
- `templates/emails/contact.html.twig` — ajout section TRAITER avec bouton lien admin + `target="_blank" rel="noopener noreferrer"`

## Notes techniques
- Utilisation de la route EasyAdmin générée automatiquement `admin_contact_message_detail` (paramètre `entityId`) pour éviter tout conflit de chemin.
- `target="_blank"` indispensable pour les webmails (Gmail, etc.) qui rendent les emails dans un iframe sandboxé — sans lui, le navigateur produit `about:blank#blocked`.
