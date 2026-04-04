# Qui reçoit un mail et quand — CF2m

**Dernière mise à jour** : 2026-04-04

---

## Vue d'ensemble

| Déclencheur | Expéditeur logique | Destinataire(s) | Template |
|---|---|---|---|
| Préinscription à une formation | CF2m — Préinscriptions | Tous les `ROLE_ADMIN` | `inscription_admin.html.twig` |
| Préinscription à une formation | CF2m — Centre de Formation | L'internaute qui s'est inscrit | `inscription_confirmation.html.twig` |
| Formulaire de contact | CF2m — Contact | `MAIL_ADMIN` (variable d'env) | `contact.html.twig` |
| Création d'un compte utilisateur par un admin | CF2m Administration | Le nouvel utilisateur | `user_bienvenue.html.twig` |
| Demande de réinitialisation de mot de passe | CF2m Administration | L'utilisateur connecté | `reset_password.html.twig` |
| Révision Works soumise par un stagiaire | CF2m — Révisions | Responsables de la formation parente | `revision_pending.html.twig` |
| Révision Formation/Page soumise (PENDING) | CF2m — Révisions | Tous les `ROLE_ADMIN` | `revision_pending.html.twig` |
| Révision approuvée | CF2m — Révisions | L'auteur de la révision | `revision_decision.html.twig` |
| Révision rejetée | CF2m — Révisions | L'auteur de la révision | `revision_decision.html.twig` |

---

## Détail par déclencheur

### 1. Préinscription à une formation en recrutement

**Source** : `InscriptionController::create()` — route `POST /preinscription/{formationSlug}`  
**Condition** : formation avec `status = 'recruiting'`, formulaire valide, Turnstile OK

**Mail 1 — Notification aux admins et pédagos**
- **Destinataires** : tous les utilisateurs ayant `ROLE_ADMIN`, `ROLE_SUPER_ADMIN` ou `ROLE_PEDAGO` (via `UserRepository::findInscriptionRecipients()`)
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

- **Destinataire principal** : adresse fixe `MAIL_ADMIN` (variable d'env)
- **Copies** : tous les `ROLE_PEDAGO` (via `UserRepository::findContactRecipients()`) — sauf si leur email est identique à `MAIL_ADMIN`
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
| `MAIL_FORM` | Expéditeur des mails préinscription/contact | À définir dans `.env.local` |

En préprod/prod : `MAILER_DSN=mailjet+api://...` défini dans `.env.local` sur le VPS (jamais versionné).

---

## Récapitulatif par rôle destinataire

| Rôle / Profil | Reçoit |
|---|---|
| `ROLE_ADMIN` | Nouvelles préinscriptions · Révisions Formation/Page en attente |
| `ROLE_PEDAGO` | Nouvelles préinscriptions · Messages de contact (copie) |
| Responsables d'une formation (`formation_user`) | Révisions Works en attente sur leurs formations |
| Auteur d'une révision (tout rôle) | Décision (approbation ou rejet) sur sa révision |
| Nouvel utilisateur (tout rôle) | Mail de bienvenue avec identifiants |
| Utilisateur connecté (tout rôle) | Lien de réinitialisation de mot de passe |
| Internaute (non connecté) | Accusé de réception de préinscription |
| `MAIL_ADMIN` (adresse fixe) | Messages du formulaire de contact (destinataire principal) |
