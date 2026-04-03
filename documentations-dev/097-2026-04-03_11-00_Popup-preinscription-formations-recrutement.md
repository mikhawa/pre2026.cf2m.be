# 097 — Popup de préinscription pour les formations en recrutement

**Date** : 2026-04-03  
**Branche** : `feature/10-formation-statement`  
**Modèles utilisés** : Sonnet (tâches 128, 129), Haiku (tâche 130)

---

## Contexte

La branche `feature/10-formation-statement` introduit le statut `recruiting` sur les formations.
Les formations en recrutement doivent être mises en avant sur le frontend et permettre
une demande de préinscription via un formulaire modal sécurisé.

---

## Fonctionnalité : formations "Recrutement" visibles (tâche 128)

### Objectif
Rendre les formations `recruiting` accessibles sur le frontend, triées avant les `published`.

### Fichiers modifiés
| Fichier | Modification |
|---|---|
| `src/Repository/FormationRepository.php` | `findAllPublished()` inclut `recruiting` + `published`, triées via `CASE WHEN` Doctrine HIDDEN |
| `src/Controller/FormationController.php` | Condition d'accès étendue à `['published', 'recruiting']` |
| `templates/base.html.twig` | Badge "Recrutement" dans le dropdown navbar |
| `templates/home/index.html.twig` | Badge jaune + bouton "S'inscrire" (warning) sur les cartes en recrutement |
| `templates/formation/show.html.twig` | Badge contextuel : "Recrutement en cours" (warning) ou "Ouverte" |

---

## Fonctionnalité : popup de préinscription (tâche 129)

### Objectif
Le bouton "S'inscrire" sur une formation `recruiting` ouvre une modal Bootstrap avec un formulaire
de préinscription protégé par CSRF + Cloudflare Turnstile.

### Champs du formulaire
| Champ | Type | Obligatoire | Contraintes |
|---|---|---|---|
| Nom | text | oui | NotBlank |
| Prénom | text | oui | NotBlank |
| E-mail | email | oui | NotBlank + Email valide |
| Téléphone | tel | oui | NotBlank — ajouté en tâche 130 |
| Âge | integer | oui | NotBlank + Range 16–99 |
| Message | textarea | non | — |

### Fichiers créés
| Fichier | Description |
|---|---|
| `src/Entity/Inscription.php` | Champs `age` (INT UNSIGNED) et `telephone` (VARCHAR 30) ajoutés |
| `src/Form/InscriptionType.php` | Formulaire Symfony : nom, prénom, email, telephone, age, message |
| `src/Controller/InscriptionController.php` | Route `POST /preinscription/{formationSlug}` — validation CSRF + Turnstile, persist, emails |
| `templates/emails/inscription_admin.html.twig` | Notification HTML aux ROLE_ADMIN |
| `templates/emails/inscription_confirmation.html.twig` | Accusé de réception HTML à l'expéditeur |
| `migrations/Version20260403100000.php` | `ALTER TABLE inscription ADD age INT UNSIGNED NOT NULL` |
| `migrations/Version20260403110000.php` | `ALTER TABLE inscription ADD telephone VARCHAR(30) NOT NULL` |
| `migrations/Version20260403120000.php` | Migration corrective : suppression du `DEFAULT ''` résiduel sur `telephone`, `SMALLINT → INT` sur `age` |

### Fichiers modifiés
| Fichier | Modification |
|---|---|
| `src/Controller/FormationController.php` | Crée et passe `inscriptionForm` (vide) au template si `status = recruiting` |
| `src/Controller/Admin/InscriptionCrudController.php` | Champs `telephone` et `age` ajoutés (masqués dans la liste, visibles en détail/édition) |
| `templates/formation/show.html.twig` | Modal Bootstrap + formulaire Symfony + Turnstile |
| `assets/styles/app.css` | Classe `.cf2m-modal` — styles dark/light pour les modals génériques |

### Flux de soumission
```
Clic "S'inscrire"
  → Modal Bootstrap s'ouvre (data-bs-toggle natif)
  → Soumission du formulaire (data-turbo="false" → rechargement natif)
  → Controller : validation CSRF Symfony + Turnstile
  → Succès : persist BDD + email admin + email AR → redirect + flash inscription_success
             → page rechargée, modal s'ouvre via <script type="module"> + message de remerciement
  → Erreur validation : render 200 + showInscriptionModal=true
             → page rechargée, modal s'ouvre via <script type="module"> + erreurs inline
  → Erreur Turnstile : render 200 + showInscriptionModal=true + turnstileError
             → page rechargée, modal s'ouvre + alerte rouge en tête du formulaire
```

### Emails envoyés
- **Notification admin** : envoyée à tous les utilisateurs `ROLE_ADMIN` et `ROLE_SUPER_ADMIN` (via `UserRepository::findAdmins()`) — contient nom, prénom, email, téléphone, âge, message, formation, date
- **Accusé de réception** : envoyé à l'adresse saisie — récapitulatif complet de la demande

---

## Dark mode — modal `.cf2m-modal` (correction)

### Problème
En dark mode, le texte du popup était illisible : `:root` surcharge `--bs-body-color` en blanc
mais `--bs-body-bg` reste sur le blanc Bootstrap → texte blanc sur fond blanc.

### Solution
Classe `cf2m-modal` ajoutée sur `.modal-content` avec styles explicites dans `assets/styles/app.css` :
- **Dark** : fond `#0d1e2e`, texte `rgba(255,255,255,0.87)`, champs semi-transparents, `btn-close` inversé
- **Light** (`[data-theme="light"]`)  : fond blanc, texte `#1a2b3c`, champs Bootstrap standards

---

## Correction : modal qui se ferme malgré les erreurs

### Problème
Avec Turbo Drive, les réponses `200` sur un POST sont ignorées (Turbo attend un `3xx`).
L'ouverture de la modal via `DOMContentLoaded` ne fonctionnait pas non plus car `bootstrap`
est importé en ESM dans `app.js` et n'est pas accessible en variable globale depuis un `<script>` inline.

### Solution finale
1. **`data-turbo="false"`** sur le formulaire → soumission native (rechargement complet), sans interférence Turbo
2. **`<script type="module">`** pour l'ouverture JS : les inline module scripts partagent l'importmap de `app.js`, donc `import { Modal } from 'bootstrap'` fonctionne et Bootstrap gère intégralement le cycle de vie (backdrop inclus)

```twig
{% if modalOpen %}
<script type="module">
    import { Modal } from 'bootstrap';
    Modal.getOrCreateInstance(document.getElementById('modalInscription')).show();
</script>
{% endif %}
```

La variable `modalOpen` est vraie si `successFlashes is not empty` (succès redirect) ou `showInscriptionModal is defined` (erreur de validation/Turnstile).

---

## Migrations exécutées

| Migration | Description | Statut |
|---|---|---|
| `Version20260403100000` | Ajout colonne `age INT UNSIGNED` | ✅ exécutée |
| `Version20260403110000` | Ajout colonne `telephone VARCHAR(30)` | ✅ exécutée |
| `Version20260403120000` | Correction types (DEFAULT '', SMALLINT→INT) | ✅ exécutée |
