# 022 — Page "Nous contacter" avec formulaire protégé anti-robots

**Date** : 2026-03-07
**Modèle** : Sonnet (form type, controller, templates, CSS)
**Justification** : Controller + formulaire Symfony + protection anti-bots.

## Fichiers créés / modifiés

- `src/Form/ContactType.php` — créé : champs nom, email, sujet, message + champ honeypot `url` (Blank constraint)
- `src/Controller/ContactController.php` — créé : routes `app_contact` et `app_contact_success`, persistance en BDD, vérification honeypot
- `templates/contact/index.html.twig` — créé : formulaire avec honeypot masqué CSS
- `templates/contact/success.html.twig` — créé : page de confirmation
- `templates/base.html.twig` — lien "Nous contacter" → `path('app_contact')`
- `assets/styles/app.css` — styles `.cf2m-honeypot`, `.cf2m-input`, `.cf2m-btn-submit`

## Protection anti-robots

1. **CSRF token** — automatique via Symfony forms (protection POST forgery)
2. **Honeypot** — champ `url` masqué CSS (`position: absolute; left: -9999px`), `tabindex="-1"`, `autocomplete="off"`. Si rempli → silently ignore (redirection succès sans persistance)

## Résultat

Page `/contact` fonctionnelle, messages sauvegardés en BDD dans `ContactMessage`.
