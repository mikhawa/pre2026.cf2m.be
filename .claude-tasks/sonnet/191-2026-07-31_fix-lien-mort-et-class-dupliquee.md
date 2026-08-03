# Tâche : Corriger le lien "mot de passe oublié" mort et le bug de class dupliquée

**Numéro** : 191
**Date** : 2026-07-31
**Modèle utilisé** : Sonnet
**Justification du modèle** : Ajout d'une route/contrôleur métier (envoi d'e-mail, anti-énumération de comptes, vérification Turnstile) en réutilisant les patterns existants de `ProfileController`/`ContactController` — au-delà d'un CRUD simple, mais sans enjeu d'architecture globale justifiant Opus.
**Complexité** : Moyenne
**Fichiers concernés** : `src/Controller/SecurityController.php`, `templates/security/forgot_password.html.twig` (nouveau), `templates/security/login.html.twig`, `templates/profil/index.html.twig`

## Contexte nécessaire
Revue UI/UX du front public (voir conversation) : le lien "Mot de passe oublié ?" de `security/login.html.twig` pointait vers `href="#"` car aucune route publique n'existait pour qu'un utilisateur déconnecté demande une réinitialisation (seule `ProfileController::requestPasswordReset`, réservée aux utilisateurs connectés, existait). Par ailleurs, `profil/index.html.twig` contenait un attribut `class` dupliqué sur 3 `<h3>` (le second `class="cf2m-muted-label"` étant silencieusement ignoré par le navigateur).

## Objectif
- Corriger le bug de `class` dupliqué (style `cf2m-muted-label` qui ne s'appliquait jamais).
- Construire le flux complet de demande de réinitialisation pour utilisateur déconnecté (choix validé par l'utilisateur plutôt que de simplement retirer le lien).

## Contraintes
- Réutiliser la logique de génération de token déjà présente dans `ProfileController::requestPasswordReset` (même champs `resetPasswordToken`/`resetPasswordRequestedAt`, même template d'e-mail `emails/reset_password.html.twig`).
- Ne pas révéler si un e-mail correspond à un compte existant (message générique dans tous les cas).
- Protéger le formulaire par Cloudflare Turnstile, comme les autres formulaires publics (`ContactController`, `InscriptionController`).
- Respecter la charte visuelle `cf2m-login-*` des pages d'authentification.

## Critères d'acceptation
- [x] `lint:twig` OK sur les templates modifiés/créés
- [x] `lint:container` OK (nouvelles dépendances du contrôleur bien injectées)
- [x] Route `app_forgot_password` (`/mot-de-passe-oublie`) enregistrée et accessible sans authentification (hors `access_control` de `security.yaml`)
- [x] Lien de `login.html.twig` pointe vers la nouvelle route

## Résultat
- `templates/profil/index.html.twig` : 3 occurrences de `class="..." class="cf2m-muted-label"` fusionnées en une seule liste de classes.
- `SecurityController` : nouveau constructeur (`MailerInterface`, `MAIL_FORM`, `TurnstileVerifier`) et nouvelle action `forgotPassword()` (route `app_forgot_password`) : formulaire e-mail + Turnstile, recherche de l'utilisateur, envoi de l'e-mail de réinitialisation si trouvé, message flash générique dans tous les cas.
- Nouveau template `templates/security/forgot_password.html.twig` sur le modèle de `reset_password.html.twig`/`login.html.twig` (widget Turnstile en `data-theme="auto"` plutôt qu'une valeur figée, pour ne pas reproduire l'incohérence de thème repérée ailleurs).
- `templates/security/login.html.twig:85` : `href="#"` remplacé par `{{ path('app_forgot_password') }}`.
