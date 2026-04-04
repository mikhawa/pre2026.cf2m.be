# 100 — Création du rôle ROLE_PEDAGO et restrictions de permissions

**Date** : 2026-04-04
**Branche** : `fix/02-popup-size-responsive-and-divers`

---

## Fichiers modifiés

### Sécurité / Voters
- `config/packages/security.yaml` — hiérarchie de rôles
- `src/Security/Voter/ContentManagerVoter.php` *(créé)*
- `src/Security/Voter/FormationVoter.php`
- `src/Security/Voter/WorksVoter.php`

### Controllers EasyAdmin
- `src/Controller/Admin/FormationCrudController.php`
- `src/Controller/Admin/WorksCrudController.php`
- `src/Controller/Admin/PageCrudController.php`
- `src/Controller/Admin/InscriptionCrudController.php`
- `src/Controller/Admin/ContactMessageCrudController.php`
- `src/Controller/Admin/UserCrudController.php`
- `src/Controller/Admin/DashboardController.php`

### Mails
- `src/Repository/UserRepository.php` — `findInscriptionRecipients()`, `findContactRecipients()`
- `src/Controller/InscriptionController.php`
- `src/Controller/ContactController.php`
- `templates/emails/inscription_admin.html.twig`

### Documentation
- `docs/architecture/permissions-fines-formations.md` *(refonte complète)*
- `docs/architecture/permissions-et-mails.md` *(créé)*

### Tests
- `tests/Security/Voter/ContentManagerVoterTest.php` *(créé)*
- `tests/Security/Voter/FormationVoterTest.php` *(créé)*
- `tests/Security/Voter/WorksVoterTest.php` *(créé)*

---

## Résumé des changements

### 1. Nouveau rôle ROLE_PEDAGO

Hiérarchie dans `security.yaml` :
```yaml
ROLE_PEDAGO: [ROLE_FORMATEUR, ROLE_STAGIAIRE, ROLE_USER]
```
Parallèle à `ROLE_ADMIN`, sans lien de dépendance entre les deux.

### 2. Voter CONTENT_MANAGER (nouveau)

`ContentManagerVoter` introduit l'attribut `CONTENT_MANAGER` :
- Accordé si `ROLE_ADMIN` **ou** `ROLE_PEDAGO`
- Utilisé dans `setPermission()` EasyAdmin pour Pages, Inscriptions, Messages de contact, Utilisateurs

### 3. Permissions par ressource

| Ressource       | ROLE_PEDAGO                          | ROLE_ADMIN                    |
|----------------|--------------------------------------|-------------------------------|
| Formations      | Créer + modifier les siennes (auto-approve) | Idem + approuver tout |
| Works           | Lecture seule (pas de NEW/EDIT)      | Accès complet                 |
| Pages           | Accès complet (CONTENT_MANAGER)      | Idem                          |
| Inscriptions    | Gérer (CONTENT_MANAGER)              | Idem                          |
| Messages contact| Lire + marquer lu (CONTENT_MANAGER)  | Idem                          |
| Utilisateurs    | Créer/éditer (CONTENT_MANAGER), rôles limités | Idem + assigner ROLE_ADMIN |

### 4. Restriction création d'utilisateurs pour ROLE_PEDAGO

Un ROLE_PEDAGO (sans ROLE_ADMIN) ne peut attribuer que :
- `ROLE_STAGIAIRE`, `ROLE_FORMATEUR`, `ROLE_PEDAGO`

Protection appliquée à trois niveaux :
- **`configureFields()`** : sélecteur de rôles filtré côté UI
- **`persistEntity()`** : filtre serveur-side à la création
- **`updateEntity()`** : filtre serveur-side à la modification

Comportement en cas de cumul `ROLE_ADMIN + ROLE_PEDAGO` : l'utilisateur hérite des droits ADMIN (condition `isPedago && !isAdmin`).

### 5. Mails

ROLE_PEDAGO reçoit :
- **Mails de préinscription** (`findInscriptionRecipients()`) — comme ROLE_ADMIN
- **Mails de contact** (`findContactRecipients()`) — comme ROLE_ADMIN
- **Pas** les mails de révision (formations/pages/works)

Le mail de préinscription inclut désormais un lien CTA direct vers la liste des inscriptions dans l'admin.

### 6. Tests

165 tests, 384 assertions — OK.
Trois nouveaux fichiers de tests voter créés avec `#[DataProvider]` (PHPUnit 13).

---

## Raison

Besoin d'un rôle intermédiaire entre ROLE_ADMIN et ROLE_FORMATEUR pour les responsables pédagogiques :
accès étendu à la gestion du contenu et des inscriptions, sans pouvoir élever les droits d'autres utilisateurs.
