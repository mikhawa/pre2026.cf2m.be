# Permissions fines par Formation — État implémenté

**Dernière mise à jour** : 2026-04-04  
**Statut** : Implémenté et en production

---

## Hiérarchie des rôles

```
ROLE_USER
  └── ROLE_STAGIAIRE
        └── ROLE_FORMATEUR
              └── ROLE_ADMIN
                    └── ROLE_SUPER_ADMIN
```

Défini dans `config/packages/security.yaml`.

---

## Principe des permissions contextuelles

Un `ROLE_FORMATEUR` désigné comme **responsable** d'une Formation (relation ManyToMany `formation_user`) obtient les mêmes droits qu'un `ROLE_ADMIN` **sur cette formation uniquement** — et sur les Works dont cette formation est parente.

---

## Voters Symfony

### `FormationVoter` — `src/Security/Voter/FormationVoter.php`

| Attribut | Accordé si |
|---|---|
| `FORMATION_EDIT_AUTOAPPROVE` | `ROLE_ADMIN` **ou** (`ROLE_FORMATEUR` + responsable de la formation) |
| `FORMATION_APPROVE` | idem |
| `FORMATION_REJECT` | idem |
| `FORMATION_RESTORE` | idem |

**Sujet attendu** : instance de `Formation`.

### `WorksVoter` — `src/Security/Voter/WorksVoter.php`

| Attribut | Accordé si |
|---|---|
| `WORKS_EDIT_AUTOAPPROVE` | `ROLE_ADMIN` **ou** (`ROLE_FORMATEUR` + responsable de la formation parente du Works) |
| `WORKS_APPROVE` | idem |
| `WORKS_REJECT` | idem |
| `WORKS_RESTORE` | idem |

**Sujet attendu** : instance de `Works`. Le voter remonte à `$works->getFormation()` pour vérifier les responsables. Si la formation parente est `null`, accès refusé.

---

## Comportement par rôle

### Formations

| Rôle | Voir la liste | Créer | Modifier | Supprimer | Historique | Approuver/Rejeter/Restaurer |
|---|---|---|---|---|---|---|
| ROLE_STAGIAIRE | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| ROLE_FORMATEUR (non responsable) | ✗ (filtré) | ✗ | ✗ | ✗ | ✓ (ses formations) | ✗ |
| ROLE_FORMATEUR (responsable) | ✓ (ses formations) | ✗ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_SUPER_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✓ | ✓ | ✓ |

**Filtre liste** (`createIndexQueryBuilder`) : un formateur non-admin ne voit que les formations dont il est responsable (jointure sur `formation_user`).

### Works

| Rôle | Voir la liste | Créer | Modifier | Supprimer | Historique | Approuver/Rejeter/Restaurer |
|---|---|---|---|---|---|---|
| ROLE_STAGIAIRE | ✓ (ses works) | ✓ | ✓ → PENDING | ✗ | ✗ | ✗ |
| ROLE_FORMATEUR (non responsable) | ✗ (filtré) | ✓ | ✗ | ✗ | ✓ (ses formations) | ✗ |
| ROLE_FORMATEUR (responsable) | ✓ (formations dont responsable) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✗ | ✓ | ✓ |
| ROLE_SUPER_ADMIN | ✓ (tout) | ✓ | ✓ AUTO_APPROVED | ✓ | ✓ | ✓ |

**Filtre liste** (`createIndexQueryBuilder`) :
- Stagiaire → jointure `works_user`, uniquement les works où il est étudiant
- Formateur non-admin → jointure `formation.responsables`, uniquement les works des formations dont il est responsable
- Admin/Super-admin → tout

**Édition** (action `edit`) : vérification supplémentaire avant que EasyAdmin ne traite la requête :
- Stagiaire : doit être dans `works.users`
- Formateur non-admin : doit passer `WORKS_APPROVE` (= être responsable de la formation parente)

---

## Workflow de révision

### Formations

| Acteur | Résultat de la modification |
|---|---|
| ROLE_FORMATEUR non responsable | Accès refusé (impossible d'atteindre l'écran d'édition) |
| ROLE_FORMATEUR responsable | Révision `AUTO_APPROVED` (contenu live mis à jour immédiatement) |
| ROLE_ADMIN / ROLE_SUPER_ADMIN | Révision `AUTO_APPROVED` |

### Works

| Acteur | Résultat de la modification |
|---|---|
| ROLE_STAGIAIRE (dans le works) | Révision `PENDING` — notifie les responsables de la formation parente |
| ROLE_FORMATEUR responsable | Révision `AUTO_APPROVED` |
| ROLE_ADMIN / ROLE_SUPER_ADMIN | Révision `AUTO_APPROVED` |

---

## Notifications (`RevisionService`)

- **`notifyFormateurs()`** : notifie uniquement les responsables de la formation parente du Works (`$formation->getResponsables()`), pas tous les formateurs.
- **`notifyAdmins()`** : notifie tous les utilisateurs ayant `ROLE_ADMIN`.

---

## Points de contrôle dans le code

### `FormationCrudController`

| Méthode | Vérification |
|---|---|
| `configureActions()` | `Action::NEW` → `ROLE_ADMIN` ; `Action::DELETE` → `ROLE_SUPER_ADMIN` ; `historiqueFormation` → `ROLE_FORMATEUR` |
| `createIndexQueryBuilder()` | Filtre formateur non-admin sur `responsables` |
| `configureCrud()` | `denyAccessUnlessGranted('ROLE_FORMATEUR')` |
| `updateEntity()` | `isGranted('FORMATION_EDIT_AUTOAPPROVE', $formation)` → détermine PENDING ou AUTO_APPROVED |
| `approuverHistoriqueFormation()` | `denyAccessUnlessGranted('FORMATION_APPROVE', $formation)` |
| `rejeterHistoriqueFormation()` | `denyAccessUnlessGranted('FORMATION_REJECT', $formation)` |
| `restaurerHistoriqueFormation()` | `denyAccessUnlessGranted('FORMATION_RESTORE', $formation)` |

### `WorksCrudController`

| Méthode | Vérification |
|---|---|
| `configureActions()` | `Action::NEW` → `ROLE_FORMATEUR` ; `historiqueWorks` → `ROLE_FORMATEUR` |
| `createIndexQueryBuilder()` | Filtre stagiaire sur `users` ; filtre formateur non-admin sur `formation.responsables` |
| `edit()` | Vérifie appartenance (stagiaire) ou `WORKS_APPROVE` (formateur non-admin) |
| `updateEntity()` | `isGranted('WORKS_EDIT_AUTOAPPROVE', $works)` → détermine PENDING ou AUTO_APPROVED |
| `approuverHistoriqueWorks()` | `denyAccessUnlessGranted('WORKS_APPROVE', $works)` |
| `rejeterHistoriqueWorks()` | `denyAccessUnlessGranted('WORKS_REJECT', $works)` |
| `restaurerHistoriqueWorks()` | `denyAccessUnlessGranted('WORKS_RESTORE', $works)` |

### Templates

| Template | Vérification |
|---|---|
| `templates/admin/formation/historique.html.twig` | `{% if is_granted('FORMATION_APPROVE', formation) %}` |
| `templates/admin/works/historique.html.twig` | `{% if is_granted('WORKS_APPROVE', works) %}` |

---

## Relation pivot

`Formation.responsables` — ManyToMany vers `User`, table `formation_user` (existait avant l'implémentation des voters, aucune migration nécessaire).

```php
// FormationVoter — logique de décision
if ($this->security->isGranted('ROLE_ADMIN')) {
    return true;
}
if (!$this->security->isGranted('ROLE_FORMATEUR')) {
    return false;
}
return $formation->getResponsables()->contains($user);
```

```php
// WorksVoter — idem via la formation parente
return $formation->getResponsables()->contains($user);
```
