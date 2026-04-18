# Qui reçoit un mail et quand — CF2m

**Dernière mise à jour** : 2026-04-18 (restriction emails inscription et contact à ROLE_SUPER_ADMIN + ROLE_PEDAGO)

---

## Vue d'ensemble

| Déclencheur | Expéditeur logique | Destinataire(s) | Template |
|---|---|---|---|
| **Double authentification (2FA)** | CF2m — Sécurité | L'utilisateur qui se connecte | `two_factor_code.html.twig` |
| Préinscription à une formation | CF2m — Préinscriptions | `ROLE_SUPER_ADMIN` + `ROLE_PEDAGO` | `inscription_admin.html.twig` |
| Préinscription à une formation | CF2m — Centre de Formation | L'internaute qui s'est inscrit | `inscription_confirmation.html.twig` |
| Formulaire de contact | CF2m — Contact | `MAIL_ADMIN` (fixe) + `ROLE_PEDAGO` (copies) | `contact.html.twig` |
| Création d'un compte utilisateur par un admin | CF2m Administration | Le nouvel utilisateur | `user_bienvenue.html.twig` |
| Demande de réinitialisation de mot de passe | CF2m Administration | L'utilisateur connecté | `reset_password.html.twig` |
| Révision Works soumise par un stagiaire | CF2m — Révisions | Responsables de la formation parente | `revision_pending.html.twig` |
| Révision Formation/Page soumise (PENDING) | CF2m — Révisions | Tous les `ROLE_ADMIN` | `revision_pending.html.twig` |
| Révision approuvée | CF2m — Révisions | L'auteur de la révision | `revision_decision.html.twig` |
| Révision rejetée | CF2m — Révisions | L'auteur de la révision | `revision_decision.html.twig` |

---

## Détail par déclencheur

### 0. Double authentification (2FA)

**Source** : `TwoFactorLoginSubscriber::onLoginSuccess()` — déclenché sur `LoginSuccessEvent`  
**Condition** : connexion réussie d'un utilisateur ayant `ROLE_SUPER_ADMIN`, `ROLE_ADMIN` ou `ROLE_PEDAGO`

- **Destinataire** : l'utilisateur qui vient de se connecter (son propre email)
- **Expéditeur** : `MAIL_FORM` avec alias `CF2m — Sécurité`
- **Sujet** : `[CF2m] Votre code de connexion : {code}`
- **Contenu** : code à 6 chiffres, valable 15 minutes, affiché en grand dans l'email
- **Template** : `templates/emails/two_factor_code.html.twig`
- **Route de vérification** : `GET/POST /double-authentification` (`app_two_factor`)
- **Renvoi du code** : `POST /double-authentification/renvoyer` (`app_two_factor_resend`)

**Sécurité** : comparaison `hash_equals` (timing-safe), code à usage unique (effacé après validation ou expiration), session `2fa_verified` remise à zéro à la déconnexion.

---

### 1. Préinscription à une formation en recrutement

**Source** : `InscriptionController::create()` — route `POST /preinscription/{formationSlug}`  
**Condition** : formation avec `status = 'recruiting'`, formulaire valide, Turnstile OK

**Mail 1 — Notification aux super-admins et pédagos**
- **Destinataires** : tous les utilisateurs ayant `ROLE_SUPER_ADMIN` ou `ROLE_PEDAGO` (via `UserRepository::findInscriptionRecipients()`) — `ROLE_ADMIN` exclu pour éviter les doublons
- **Reply-To** : email de l'internaute inscrit
- **Sujet** : `[CF2m] Nouvelle préinscription — {titre formation}`
- **Contenu** : nom, prénom, email, téléphone, âge, message (si renseigné), date de réception
- **CTA** : bouton « Gérer les préinscriptions » → lien absolu vers `InscriptionCrudController` (liste admin)
- **Template** : `templates/emails/inscription_admin.html.twig`

**Mail 2 — Accusé de réception à l'internaute**
- **Destinataire** : l'adresse email saisie dans le formulaire
- **Sujet** : `[CF2m] Votre demande de préinscription — {titre formation}`
- **Contenu** : confirmation de réception, nom de la formation, message d'attente
- **Template** : `templates/emails/inscription_confirmation.html.twig`

---

### 2. Formulaire de contact

**Source** : `ContactController::index()` — route `POST /contact`  
**Condition** : formulaire valide, Turnstile OK

- **Destinataires** : tous les utilisateurs ayant `ROLE_SUPER_ADMIN` ou `ROLE_PEDAGO` (via `UserRepository::findContactRecipients()`) — `ROLE_ADMIN` exclu pour éviter les doublons
- **Reply-To** : email de l'expéditeur
- **Sujet** : `[CF2m] {sujet saisi}`
- **Template** : `templates/emails/contact.html.twig`

---

### 3. Création d'un compte utilisateur par un admin

**Source** : `UserCrudController::persistEntity()` — action EasyAdmin "Nouveau"  
**Condition** : création d'un nouvel utilisateur via le back-office

- **Destinataire** : l'utilisateur nouvellement créé
- **Sujet** : `Bienvenue sur CF2m — vos identifiants de connexion`
- **Contenu** : login + mot de passe généré aléatoirement (12 caractères)
- **Template** : `templates/emails/user_bienvenue.html.twig`

---

### 4. Réinitialisation de mot de passe

**Source** : `ProfileController::requestPasswordReset()` — route `GET /profil/reinitialiser-mot-de-passe`  
**Condition** : utilisateur connecté qui clique sur "Réinitialiser mon mot de passe"

- **Destinataire** : l'utilisateur connecté (son propre email)
- **Sujet** : `Réinitialisation de votre mot de passe — CF2m`
- **Contenu** : lien avec token (valable 1 heure)
- **Template** : `templates/emails/reset_password.html.twig`

---

### 5. Révision Works soumise par un stagiaire

**Source** : `RevisionService::notifyFormateurs()` — appelé depuis `WorksCrudController::updateEntity()`  
**Condition** : stagiaire modifie un Works → révision `PENDING` créée

- **Destinataires** : formateurs dans `Formation.responsables` de la formation parente du Works
- **Si aucun responsable** : aucun mail envoyé
- **Sujet** : `[CF2m] Nouvelle révision Works en attente de validation`
- **Template** : `templates/emails/revision_pending.html.twig`

---

### 6. Révision Formation ou Page soumise (PENDING)

**Source** : `RevisionService::notifyAdmins()` — appelé depuis `FormationCrudController::updateEntity()` et `PageCrudController::updateEntity()`  
**Condition** : formateur (non-responsable pour Formation, ou tout formateur pour Page) modifie → révision `PENDING`

- **Destinataires** : tous les utilisateurs ayant `ROLE_ADMIN` (via `UserRepository::findAdmins()`)
- **Sujet** : `[CF2m] Nouvelle révision en attente de validation`
- **Template** : `templates/emails/revision_pending.html.twig`

---

### 7. Révision approuvée ou rejetée

**Source** : `RevisionService::notifyAuthorFromHistory()` → `notifyAuthor()`  
**Appelé depuis** :
- `FormationCrudController::approuverHistoriqueFormation()` / `rejeterHistoriqueFormation()`
- `PageCrudController::approuverHistoriquePage()` / `rejeterHistoriquePage()`
- `WorksCrudController::approuverHistoriqueWorks()` / `rejeterHistoriqueWorks()`

**Condition** : un admin/responsable approuve ou rejette une révision en attente

- **Destinataire** : l'auteur de la révision (`history.createdBy`)
- **Si l'auteur n'a pas d'email** : aucun mail envoyé
- **Sujet approuvé** : `[CF2m] Votre révision a été approuvée — {titre}`
- **Sujet rejeté** : `[CF2m] Votre révision a été rejetée — {titre}`
- **Template** : `templates/emails/revision_decision.html.twig`

---

## Variables d'environnement impliquées

| Variable | Rôle | Valeur dev (`.env`) |
|---|---|---|
| `MAILER_DSN` | Transport SMTP | `smtp://mailpit:1025` (Mailpit local) |
| `MAIL_ADMIN` | Destinataire du formulaire de contact | À définir dans `.env.local` |
| `MAIL_FROM` | Expéditeur de tous les mails applicatifs | À définir dans `.env.local` |
| `MAIL_FORM` | Expéditeur des mails préinscription/contact/2FA | À définir dans `.env.local` |

En préprod/prod : `MAILER_DSN=mailjet+api://...` défini dans `.env.local` sur le VPS (jamais versionné).

### Redirection des emails en mode dev

En `APP_ENV=dev`, **tous les emails** (y compris les codes 2FA) sont redirigés vers `michaeljpitz@gmail.com` via `config/packages/dev/mailer.yaml` (envelope Symfony).

- Dev local avec Mailpit : Mailpit intercepte tout de toute façon, l'envelope n'a pas d'effet visible
- Préprod en `APP_ENV=dev` avec Mailjet : les emails partent réellement mais arrivent à `michaeljpitz@gmail.com`
- Préprod/Prod en `APP_ENV=prod` : ce fichier n'est pas chargé, les emails partent aux vraies adresses

---

## Récapitulatif par rôle destinataire

| Rôle / Profil | Reçoit |
|---|---|
| `ROLE_SUPER_ADMIN` | **Code 2FA à chaque connexion** · Nouvelles préinscriptions · Messages de contact |
| `ROLE_ADMIN` | **Code 2FA à chaque connexion** · Révisions Formation/Page en attente |
| `ROLE_PEDAGO` | **Code 2FA à chaque connexion** · Nouvelles préinscriptions · Messages de contact |
| Responsables d'une formation (`formation_user`) | Révisions Works en attente sur leurs formations |
| Auteur d'une révision (tout rôle) | Décision (approbation ou rejet) sur sa révision |
| Nouvel utilisateur (tout rôle) | Mail de bienvenue avec identifiants |
| Utilisateur connecté (tout rôle) | Lien de réinitialisation de mot de passe |
| Internaute (non connecté) | Accusé de réception de préinscription |
| `MAIL_ADMIN` (adresse fixe) | Messages du formulaire de contact (destinataire principal) |
