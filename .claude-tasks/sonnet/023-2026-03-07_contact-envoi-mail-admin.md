# 023 — Envoi email à MAIL_ADMIN sur soumission du formulaire de contact

**Date** : 2026-03-07
**Modèle** : Sonnet

## Fichiers créés / modifiés

- `src/Controller/ContactController.php` — injection `MailerInterface` + `#[Autowire(env: 'MAIL_ADMIN')]`, envoi `TemplatedEmail` après persistance
- `templates/emails/contact.html.twig` — créé : template HTML de l'email (nom, email, sujet, message, date)

## Résumé

- `From` : `MAIL_ADMIN` (avec label "CF2m — Contact")
- `To` : `MAIL_ADMIN`
- `Reply-To` : email + nom de l'expéditeur (permet de répondre directement depuis le client mail)
- `Subject` : `[CF2m] {sujet}`
- Template HTML inline-styled compatible clients mail
- Mailer DSN : Mailpit en dev (`smtp://mailpit:1025`), configurable via `.env.local` en prod
