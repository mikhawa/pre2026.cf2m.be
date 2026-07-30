# Permissions fines par Formation — État implémenté

**Dernière mise à jour** : 2026-07-02 (ajout `FormationStagiaireVoter`)
**Statut** : Implémenté et en production

---

## Hiérarchie des rôles

```
ROLE_USER
  └── ROLE_STAGIAIRE
        └── ROLE_FORMATEUR
              ├── ROLE_ADMIN
              │     └── ROLE_SUPER_ADMIN
              └── ROLE_PEDAGO
```

Défini dans `config/packages/security.yaml`. `ROLE_PEDAGO` est au même niveau que `ROLE_ADMIN` mais sans en hériter — droits distincts (voir section dédiée ci-dessous).

---

## Principe des permissions contextuelles

Un `ROLE_FORMATEUR` désigné comme **responsable** d'une Formation (relation ManyToMany `formation_user`) obtient les mêmes droits qu'un `ROLE_ADMIN` **sur cette formation uniquement** — et sur les Works dont cette formation est parente.

---

## ROLE_PEDAGO — droits spécifiques

`ROLE_PEDAGO` est un rôle pédagogique au même niveau hiérarchique que `ROLE_ADMIN`, avec un périmètre différent.

| Ressource | Droits |
|---|---|
| **Formations** | Créer (`FORMATION_CREATE`), modifier (AUTO_APPROVED), voir tout, historique |
| **Works** | Lecture seule — INDEX et DETAIL uniquement (pas de NEW, pas d'EDIT) |
| **Pages** | Accès complet via `CONTENT_MANAGER` (comme ROLE_ADMIN) |
| **Inscriptions** | Gérer via `CONTENT_MANAGER` (voir, éditer, marquer comme traité) |
| **Utilisateurs** | Créer et gérer via `CONTENT_MANAGER` |
| **Messages de contact** | Voir et marquer comme lu via `CONTENT_MANAGER` |
| **Révisions en attente** | Non accessible (menu masqué, route protégée par `ROLE_ADMIN`) |
| **Mail préinscription** | Reçoit (via `findInscriptionRecipients()`) |
| **Mail contact** | Reçoit en copie (via `findContactRecipients()`, en plus de `MAIL_ADMIN`) |
| **Mail révision** | Ne reçoit pas |

---

## Voters Symfony

### `FormationVoter` — `src/Security/Voter/FormationVoter.php`

| Attribut | Accordé si | Sujet |
|---|---|---|
| `FORMATION_CREATE` | `ROLE_ADMIN` ou `ROLE_PEDAGO` | aucun |
| `FORMATION_EDIT_AUTOAPPROVE` | `ROLE_ADMIN`, `ROLE_PEDAGO` ou (`ROLE_FORMATEUR` + responsable) | `Formation` |
| `FORMATION_APPROVE` | idem | `Formation` |
| `FORMATION_REJECT` | idem | `Formation` |
| `FORMATION_RESTORE` | idem | `Formation` |

`FORMATION_CREATE` ne requiert pas de sujet — utilisé dans `setPermission(Action::NEW, 'FORMATION_CREATE')`.

### `WorksVoter` — `src/Security/Voter/WorksVoter.php`

| Attribut | Accordé si | Sujet |
|---|---|---|
| `WORKS_EDIT_AUTOAPPROVE` | `ROLE_ADMIN` ou (`ROLE_FORMATEUR` + responsable de la formation parente) | `Works` |
| `WORKS_APPROVE` | idem | `Works` |
| `WORKS_REJECT` | idem | `Works` |
| `WORKS_RESTORE` | idem | `Works` |

Le voter remonte à `$works->getFormation()` pour vérifier les responsables. Si la formation parente est `null`, accès refusé. `ROLE_PEDAGO` n'a pas accès à ces attributs (lecture seule sur Works).

### `ContentManagerVoter` — `src/Security/Voter/ContentManagerVoter.php`

| Attribut | Accordé si | Sujet |
|---|---|---|
| `CONTENT_MANAGER` | `ROLE_ADMIN` ou `ROLE_PEDAGO` | aucun |

Utilisé dans `setPermission()`, `denyAccessUnlessGranted()` et les menus EasyAdmin pour toutes les ressources partagées entre `ROLE_ADMIN` et `ROLE_PEDAGO` : Pages, Inscriptions, Utilisateurs, Messages de contact.

### `FormationStagiaireVoter` — `src/Security/Voter/FormationStagiaireVoter.php`

| Attribut | Accordé si | Sujet |
|---|---|---|
| `FORMATION_MANAGE_STAGIAIRES` | `ROLE_ADMIN`, `ROLE_PEDAGO`, ou (`ROLE_FORMATEUR` + responsable de la formation) | `Formation` |

Toujours évalué avec un sujet `Formation` (pas de cas `CREATE` sans sujet). Contrôle l'accès à l'action EasyAdmin « Stagiaires » de `FormationCrudController` (ajout/retrait de stagiaires rattachés via l'entité pivot `FormationStagiaire`).

Voir `docs/architecture/proposition-gestion-stagiaires-formation.md` pour le contexte de conception, et `docs/architecture/database-schema.md#formationstagiaire-entité-pivot` pour le schéma.

---

## Comportement par rôle

### Formations

| Rôle | Voir la liste | Créer | Modifier | Supprimer | Historique | Approuver/Rejeter/Restaurer |
|---|---|---|---|---|---|---|
| ROLE_STAGIAIRE | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| ROLE_FORMATEUR (non responsable) | ✗ (filtré) | ✗ | ✗ | ✗ | ✓ (ses formations) | ✗ |
| ROLE_FORMATEUR (responsable) | ✓ (ses formations) | ✗ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_PEDAGO | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_SUPER_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✓ | ✓ | ✓ |

**Filtre liste** (`createIndexQueryBuilder`) : un formateur non-admin **et non-pédago** ne voit que les formations dont il est responsable.

### Works

| Rôle | Voir la liste | Créer | Modifier | Supprimer | Historique | Approuver/Rejeter/Restaurer |
|---|---|---|---|---|---|---|
| ROLE_STAGIAIRE | ✓ (ses works) | ✓ | ✓ → PENDING | ✗ | ✗ | ✗ |
| ROLE_FORMATEUR (non responsable) | ✗ (filtré) | ✓ | ✗ | ✗ | ✓ (ses formations) | ✗ |
| ROLE_FORMATEUR (responsable) | ✓ (formations dont responsable) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_PEDAGO | ✓ (tout) | ✗ | ✗ | ✗ | ✓ | ✗ |
| ROLE_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_SUPER_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✓ | ✓ | ✓ |

**Filtre liste** (`createIndexQueryBuilder`) :
- Stagiaire → jointure `works_user`, uniquement les works où il est étudiant
- Formateur non-admin **et non-pédago** → jointure `formation.responsables`
- Admin / Super-admin / Pédago → tout

**Édition** (action `edit`) : ROLE_PEDAGO est explicitement bloqué côté serveur (`createAccessDeniedException`) en plus du masquage des boutons.

### Pages

| Rôle | Voir | Créer | Modifier | Supprimer | Historique | Approuver/Rejeter/Restaurer |
|---|---|---|---|---|---|---|
| ROLE_PEDAGO | ✓ | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_ADMIN | ✓ | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_SUPER_ADMIN | ✓ | ✓ | ✓ AUTO_APPROVED | ✓ | ✓ | ✓ |

---

## Workflow de révision

### Formations

| Acteur | Résultat de la modification |
|---|---|
| ROLE_FORMATEUR non responsable | Accès refusé |
| ROLE_FORMATEUR responsable | Révision `AUTO_APPROVED` |
| ROLE_PEDAGO | Révision `AUTO_APPROVED` |
| ROLE_ADMIN / ROLE_SUPER_ADMIN | Révision `AUTO_APPROVED` |

### Works

| Acteur | Résultat de la modification |
|---|---|
| ROLE_STAGIAIRE (dans le works) | Révision `PENDING` — notifie les responsables de la formation parente |
| ROLE_FORMATEUR responsable | Révision `AUTO_APPROVED` |
| ROLE_PEDAGO | Accès refusé (lecture seule) |
| ROLE_ADMIN / ROLE_SUPER_ADMIN | Révision `AUTO_APPROVED` |

### Pages

| Acteur | Résultat de la modification |
|---|---|
| ROLE_FORMATEUR | Révision `PENDING` — notifie les admins |
| ROLE_PEDAGO | Révision `AUTO_APPROVED` |
| ROLE_ADMIN / ROLE_SUPER_ADMIN | Révision `AUTO_APPROVED` |

---

## Notifications (`RevisionService`)

- **`notifyFormateurs()`** : notifie uniquement les responsables de la formation parente du Works (`$formation->getResponsables()`).
- **`notifyAdmins()`** : notifie tous les utilisateurs ayant `ROLE_ADMIN` uniquement — `ROLE_PEDAGO` ne reçoit pas les notifications de révision.

---

## Points de contrôle dans le code

### `FormationCrudController`

| Méthode | Vérification |
|---|---|
| `configureActions()` | `Action::NEW` → `FORMATION_CREATE` ; `Action::DELETE` → `ROLE_SUPER_ADMIN` ; `historiqueFormation` → `ROLE_FORMATEUR` |
| `createIndexQueryBuilder()` | Filtre formateur non-admin **et non-pédago** sur `responsables` |
| `configureCrud()` | `denyAccessUnlessGranted('ROLE_FORMATEUR')` |
| `updateEntity()` | `isGranted('FORMATION_EDIT_AUTOAPPROVE', $formation)` → détermine PENDING ou AUTO_APPROVED |
| `approuverHistoriqueFormation()` | `denyAccessUnlessGranted('FORMATION_APPROVE', $formation)` |
| `rejeterHistoriqueFormation()` | `denyAccessUnlessGranted('FORMATION_REJECT', $formation)` |
| `restaurerHistoriqueFormation()` | `denyAccessUnlessGranted('FORMATION_RESTORE', $formation)` |

### `WorksCrudController`

| Méthode | Vérification |
|---|---|
| `configureActions()` | `Action::NEW` → `ROLE_FORMATEUR` ; PEDAGO : `disable(NEW, EDIT)` |
| `createIndexQueryBuilder()` | Filtre stagiaire sur `users` ; filtre formateur non-admin **et non-pédago** sur `formation.responsables` |
| `edit()` | Deny PEDAGO ; vérifie appartenance (stagiaire) ou `WORKS_APPROVE` (formateur non-admin) |
| `updateEntity()` | Deny PEDAGO ; `isGranted('WORKS_EDIT_AUTOAPPROVE', $works)` → détermine PENDING ou AUTO_APPROVED |
| `approuverHistoriqueWorks()` | `denyAccessUnlessGranted('WORKS_APPROVE', $works)` |
| `rejeterHistoriqueWorks()` | `denyAccessUnlessGranted('WORKS_REJECT', $works)` |
| `restaurerHistoriqueWorks()` | `denyAccessUnlessGranted('WORKS_RESTORE', $works)` |

### `PageCrudController`

| Méthode | Vérification |
|---|---|
| `configureActions()` | Toutes actions → `CONTENT_MANAGER` sauf DELETE → `ROLE_SUPER_ADMIN` |
| `historiquePage()`, `approuver*()`, `rejeter*()`, `restaurer*()` | `denyAccessUnlessGranted('CONTENT_MANAGER')` |

### `InscriptionCrudController` / `UserCrudController` / `ContactMessageCrudController`

| Controller | Vérification |
|---|---|
| `InscriptionCrudController` | INDEX, EDIT, DETAIL → `CONTENT_MANAGER` |
| `UserCrudController` | INDEX, NEW, EDIT, DETAIL → `CONTENT_MANAGER` |
| `ContactMessageCrudController` | INDEX, EDIT, DETAIL → `CONTENT_MANAGER` |

### Templates

| Template | Vérification |
|---|---|
| `templates/admin/formation/historique.html.twig` | `{% if is_granted('FORMATION_APPROVE', formation) %}` |
| `templates/admin/works/historique.html.twig` | `{% if is_granted('WORKS_APPROVE', works) %}` |

---

## Relation pivot

`Formation.responsables` — ManyToMany vers `User`, table `formation_user` (existait avant l'implémentation des voters, aucune migration nécessaire).

```php
// FormationVoter — logique de décision (simplifiée)
if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_PEDAGO')) {
    return true;
}
if ($attribute === self::CREATE) {
    return false; // réservé admin/pédago
}
if (!$this->security->isGranted('ROLE_FORMATEUR')) {
    return false;
}
return $formation->getResponsables()->contains($user);
```

```php
// WorksVoter — idem via la formation parente (ROLE_PEDAGO non couvert = lecture seule)
if ($this->security->isGranted('ROLE_ADMIN')) {
    return true;
}
if (!$this->security->isGranted('ROLE_FORMATEUR')) {
    return false;
}
return $formation->getResponsables()->contains($user);
```
