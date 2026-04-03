# Tâche 129 — Popup de préinscription pour les formations en recrutement

**Modèle** : Sonnet  
**Justification** : Controller métier, formulaire, templates email, modal frontend, intégration CSRF + Turnstile  
**Date** : 2026-04-03

## Fichiers modifiés

- `src/Entity/Inscription.php` — ajout du champ `age` (SMALLINT UNSIGNED, 16–99 ans, NotBlank + Range)
- `src/Controller/FormationController.php` — passe `inscriptionForm` au template si status = recruiting
- `src/Controller/Admin/InscriptionCrudController.php` — ajout du champ `age` dans l'admin EasyAdmin

## Fichiers créés

- `migrations/Version20260403100000.php` — `ALTER TABLE inscription ADD age SMALLINT UNSIGNED NOT NULL DEFAULT 18`
- `src/Form/InscriptionType.php` — formulaire : nom, prénom, email, age, message (optionnel)
- `src/Controller/InscriptionController.php` — route `POST /preinscription/{formationSlug}`, CSRF + Turnstile, persist inscription, envoi emails admin + AR utilisateur, gestion erreurs (ré-affichage modal)
- `templates/emails/inscription_admin.html.twig` — notification email aux ROLE_ADMIN
- `templates/emails/inscription_confirmation.html.twig` — accusé de réception à l'expéditeur
- `templates/formation/show.html.twig` — réécriture : modal Bootstrap + formulaire Symfony + Turnstile, auto-ouverture si erreurs de validation, flash messages inline, bouton S'inscrire conditionnel (recruiting only)

## Résumé

Quand une formation a le statut `recruiting`, le bouton « S'inscrire » ouvre une modal Bootstrap avec :
- Titre : « Demande de préinscription pour {formation.title} »
- Champs : nom, prénom, e-mail, âge (16–99), message (optionnel)
- Protection CSRF (Symfony form) + Cloudflare Turnstile
- À la soumission : inscription persistée en BDD, email envoyé à tous les ROLE_ADMIN, accusé de réception envoyé à l'utilisateur
- En cas d'erreur Turnstile ou validation : redirect ou ré-affichage avec modal pré-ouverte

## Résultat

Migration exécutée avec succès. Cache warmup OK. Route `app_inscription_create` correctement enregistrée.
