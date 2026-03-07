# 032 — Page "Nous contacter" avec formulaire anti-robots

**Date** : 2026-03-07 10h30

## Fichiers créés / modifiés

| Fichier | Action |
|---|---|
| `src/Form/ContactType.php` | Créé |
| `src/Controller/ContactController.php` | Créé |
| `templates/contact/index.html.twig` | Créé |
| `templates/contact/success.html.twig` | Créé |
| `templates/base.html.twig` | Lien navbar "Nous contacter" |
| `assets/styles/app.css` | Styles formulaire + honeypot |

## Résumé

- Formulaire avec : nom, email, sujet, message
- Double protection anti-robots :
  1. **CSRF token** (Symfony automatique)
  2. **Honeypot** : champ `url` invisible (CSS `position: absolute; left: -9999px`), tabindex -1, autocomplete off. Contrainte `Blank` → si rempli, le message n'est pas persisté mais le robot voit un "succès"
- Messages stockés dans la table `contact_message` (entité existante)
- Page de confirmation `/contact/merci` après envoi

## Raison

Demande utilisateur : page de contact avec mail, sujet, champ texte et protection anti-robots.
