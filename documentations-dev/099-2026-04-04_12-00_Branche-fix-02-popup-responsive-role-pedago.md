# 099 — Branche fix/02 : popup responsive, ROLE_PEDAGO, corrections diverses

**Date** : 2026-04-04  
**Branche** : `fix/02-popup-size-responsive-and-divers`

---

## 1. Popup de préinscription — correctifs responsive mobile

### Problème initial
Sur smartphone, la modal de préinscription dépassait la hauteur de l'écran. Les boutons « Annuler » et « Envoyer ma demande » étaient inaccessibles.

### Corrections appliquées (`templates/formation/show.html.twig`)

**Étape 1** — `modal-fullscreen-sm-down` : modal plein écran < 576 px (header et footer fixés).

**Étape 2** — Scroll manquant : la balise `<form>` intercalée entre `.modal-content` et `.modal-body` cassait le mécanisme flex de Bootstrap. Correction :
- `<form>` : `display:flex; flex-direction:column; flex:1 1 auto; min-height:0; overflow:hidden`
- `.modal-body` : `flex:1 1 auto; min-height:0; overflow-y:auto; -webkit-overflow-scrolling:touch`

**Étape 3** — Turnstile échoue dans une modal cachée : Cloudflare Turnstile ne rend pas le widget dans un `display:none`. Passage au rendu explicite (`?render=explicit`) avec initialisation via l'événement `shown.bs.modal` + `turnstile.reset()` à chaque réouverture.

**Fichiers modifiés** :
- `templates/formation/show.html.twig`

---

## 2. Lien administration dans le mail de préinscription

Ajout d'un bouton CTA « Gérer les préinscriptions » dans l'email envoyé aux admins/pédagos, pointant vers la liste `InscriptionCrudController` (URL absolue).

**Fichiers modifiés** :
- `src/Controller/InscriptionController.php` — génération URL absolue via `UrlGeneratorInterface::ABSOLUTE_URL`
- `templates/emails/inscription_admin.html.twig` — bouton CTA

---

## 3. Création du rôle ROLE_PEDAGO

Nouveau rôle pédagogique au même niveau hiérarchique que `ROLE_ADMIN`, avec des droits distincts.

### Hiérarchie
```
ROLE_FORMATEUR
  ├── ROLE_ADMIN → ROLE_SUPER_ADMIN
  └── ROLE_PEDAGO
```

### Droits ROLE_PEDAGO

| Ressource | Droits |
|---|---|
| Formations | Créer, modifier (AUTO_APPROVED), voir tout |
| Works | Lecture seule (INDEX + DETAIL) |
| Pages | Accès complet |
| Inscriptions | Gérer |
| Utilisateurs | Créer et gérer |
| Messages de contact | Voir, marquer comme lu |
| Révisions en attente | Non accessible |
| Mail préinscription | Reçoit |
| Mail contact | Reçoit en copie |
| Mail révision | Ne reçoit pas |

### Fichier créé
- `src/Security/Voter/ContentManagerVoter.php` — attribut `CONTENT_MANAGER` (ROLE_ADMIN || ROLE_PEDAGO)

### Fichiers modifiés
- `config/packages/security.yaml` — hiérarchie ROLE_PEDAGO
- `src/Security/Voter/FormationVoter.php` — ROLE_PEDAGO = auto-approve + attribut `FORMATION_CREATE`
- `src/Controller/Admin/FormationCrudController.php` — NEW → `FORMATION_CREATE` ; filtre index exclut PEDAGO
- `src/Controller/Admin/WorksCrudController.php` — lecture seule PEDAGO (disable + deny serveur) ; filtre index PEDAGO voit tout
- `src/Controller/Admin/PageCrudController.php` — `ROLE_ADMIN` → `CONTENT_MANAGER`
- `src/Controller/Admin/InscriptionCrudController.php` — `ROLE_ADMIN` → `CONTENT_MANAGER`
- `src/Controller/Admin/UserCrudController.php` — `ROLE_ADMIN` → `CONTENT_MANAGER` + ROLE_PEDAGO dans les choix (badge primary)
- `src/Controller/Admin/ContactMessageCrudController.php` — permissions `CONTENT_MANAGER`
- `src/Controller/Admin/DashboardController.php` — menu Pages/Utilisateurs/Inscriptions/Contact → `CONTENT_MANAGER`
- `src/Repository/UserRepository.php` — `findInscriptionRecipients()` + `findContactRecipients()`
- `src/Controller/InscriptionController.php` — `findInscriptionRecipients()`
- `src/Controller/ContactController.php` — copie aux ROLE_PEDAGO en plus de `MAIL_ADMIN`

---

## 4. GitHub Actions — Node.js 24

Ajout de `FORCE_JAVASCRIPT_ACTIONS_TO_NODE24: true` dans `.github/workflows/symfony.yml` pour forcer les actions `actions/cache@v4` et `actions/checkout@v4` à s'exécuter sous Node.js 24. La notice résiduelle *"are being forced to run on Node.js 24"* est informative et non bloquante.

**Fichier modifié** :
- `.github/workflows/symfony.yml`

---

## 5. Fixtures — champs manquants InscriptionFactory

`telephone` et `age` ajoutés dans `defaults()` de la factory pour éviter les violations de contrainte NOT NULL.

**Fichier modifié** :
- `src/Factory/InscriptionFactory.php`

---

## 6. AppFixtures — groupe `app`

Ajout de `FixtureGroupInterface` sur `AppFixtures` pour pouvoir charger uniquement cette fixture via :
```bash
symfony console doctrine:fixtures:load --group=app
```

**Fichier modifié** :
- `src/DataFixtures/AppFixtures.php`

---

## 7. Documentation architecture mise à jour

- `docs/architecture/permissions-fines-formations.md` — ajout ROLE_PEDAGO, ContentManagerVoter, tables complètes par rôle, workflow pages
- `docs/architecture/permissions-et-mails.md` — création (liste exhaustive des envois de mails + ROLE_PEDAGO)
- `docs/devops/github-actions.md` — documentation Node.js 24
