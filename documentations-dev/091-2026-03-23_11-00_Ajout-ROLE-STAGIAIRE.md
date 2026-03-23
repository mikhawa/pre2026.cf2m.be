# 091 — Ajout du rôle ROLE_STAGIAIRE

**Date** : 2026-03-23 11:00
**Branche** : main

## Fichiers modifiés
- `config/packages/security.yaml`
- `src/Controller/Admin/UserCrudController.php`

## Changements

### Hiérarchie des rôles (`security.yaml`)
Nouveau rôle `ROLE_STAGIAIRE` positionné entre `ROLE_USER` et `ROLE_FORMATEUR` :

```
ROLE_USER < ROLE_STAGIAIRE < ROLE_FORMATEUR < ROLE_ADMIN < ROLE_SUPER_ADMIN
```

```yaml
role_hierarchy:
    ROLE_STAGIAIRE:   ROLE_USER
    ROLE_FORMATEUR:   [ROLE_STAGIAIRE, ROLE_USER]
    ROLE_ADMIN:       [ROLE_FORMATEUR, ROLE_STAGIAIRE, ROLE_USER]
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_FORMATEUR, ROLE_STAGIAIRE, ROLE_USER]
```

### EasyAdmin — UserCrudController
- **Choix formulaire** : `ROLE_STAGIAIRE` (libellé "Stagiaire") ajouté entre `ROLE_USER` et `ROLE_FORMATEUR`
- **Choix ROLE_ADMIN** : inclut `Stagiaire`, `Formateur`, `Administrateur` (pas `Super Admin`)
- **Badge** : couleur `success` (vert) pour se distinguer de `ROLE_FORMATEUR` (`info` = cyan)
- **Filtre index** : `ROLE_STAGIAIRE` ajouté au filtre de la liste

### Tableau des couleurs de badges
| Rôle | Couleur Bootstrap |
|------|-------------------|
| `ROLE_SUPER_ADMIN` | `danger` (rouge) |
| `ROLE_ADMIN` | `warning` (jaune) |
| `ROLE_FORMATEUR` | `info` (cyan) |
| `ROLE_STAGIAIRE` | `success` (vert) |
| `ROLE_USER` | `secondary` (gris) |

## Raison
Les stagiaires ont besoin d'un rôle distinct pour leur donner un accès limité à l'administration (Works uniquement) sans les confondre avec les formateurs.
