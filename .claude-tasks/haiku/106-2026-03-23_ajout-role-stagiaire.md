# 106 — Ajout du rôle ROLE_STAGIAIRE

**Modèle** : Haiku
**Justification** : Ajout simple de rôle dans la hiérarchie et les listes de choix.

## Fichiers modifiés
- `config/packages/security.yaml`
- `src/Controller/Admin/UserCrudController.php`

## Résumé

Ajout de `ROLE_STAGIAIRE` dans la hiérarchie des rôles, positionné entre `ROLE_USER` et `ROLE_FORMATEUR`.

### Hiérarchie mise à jour
```
ROLE_USER < ROLE_STAGIAIRE < ROLE_FORMATEUR < ROLE_ADMIN < ROLE_SUPER_ADMIN
```

### Détails
- `security.yaml` : `ROLE_STAGIAIRE` hérite de `ROLE_USER` ; `ROLE_FORMATEUR` et supérieurs héritent de `ROLE_STAGIAIRE`
- `UserCrudController` : ajouté dans les choix (affichage + formulaire ROLE_ADMIN), badge `primary` (bleu), filtre
- Non inclus dans le filtre `responsables` de `FormationCrudController` (car en dessous de `ROLE_FORMATEUR`)
