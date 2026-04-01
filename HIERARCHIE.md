# Hiérarchie des rôles — CF2m

> Dernière mise à jour : 2026-03-30

---

## Hiérarchie des rôles

```
ROLE_SUPER_ADMIN
    └── ROLE_ADMIN
            └── ROLE_FORMATEUR
                    └── ROLE_STAGIAIRE
                            └── ROLE_USER
```

---

## Accès global (security.yaml)

| Zone | Rôle minimum requis |
|------|---------------------|
| `/admin/*` | `ROLE_STAGIAIRE` |
| `/profil/*` | `ROLE_USER` |
| Site public | Anonyme |

---

## Admin EasyAdmin — menu et permissions

### Section Contenu

| Ressource | Voir liste | Créer | Éditer | Supprimer | Historique |
|-----------|-----------|-------|--------|-----------|------------|
| **Formations** | `ROLE_FORMATEUR` | `ROLE_ADMIN` | `ROLE_FORMATEUR`¹ | `ROLE_SUPER_ADMIN` | `ROLE_FORMATEUR` |
| **Works** | `ROLE_STAGIAIRE`² | `ROLE_FORMATEUR` | `ROLE_STAGIAIRE`³ | `ROLE_SUPER_ADMIN` | `ROLE_FORMATEUR` |
| **Pages** | `ROLE_ADMIN` | — | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` | — |

> ¹ Formateur peut éditer, mais la révision passe en attente (auto-approve = false)
> ² Stagiaire ne voit que les Works auxquels il appartient
> ³ Stagiaire ne peut éditer que les Works dont il fait partie

### Section Utilisateurs

| Action | Rôle requis |
|--------|-------------|
| Voir / Créer / Éditer | `ROLE_ADMIN` |
| Supprimer | `ROLE_SUPER_ADMIN` |
| Modifier les rôles jusqu'à `ROLE_ADMIN` | `ROLE_ADMIN` |
| Attribuer `ROLE_SUPER_ADMIN` | `ROLE_SUPER_ADMIN` uniquement |
| Éditer/voir un `ROLE_SUPER_ADMIN` | `ROLE_SUPER_ADMIN` uniquement |

| Ressource | Rôle requis |
|-----------|-------------|
| **Inscriptions** | `ROLE_ADMIN` |

### Section Interactions

| Ressource | Rôle requis |
|-----------|-------------|
| **Commentaires** | `ROLE_STAGIAIRE` |
| **Notes** | `ROLE_ADMIN` |
| **Révisions en attente** | `ROLE_ADMIN` |

### Section Communication

| Ressource | Rôle requis |
|-----------|-------------|
| **Messages de contact** | `ROLE_ADMIN` |
| **Partenaires** | `ROLE_ADMIN` |

---

## Résumé par rôle

| Rôle | Ce qu'il peut faire |
|------|---------------------|
| `ROLE_USER` | Accès profil uniquement, site public |
| `ROLE_STAGIAIRE` | + Admin (lecture commentaires, ses propres Works) |
| `ROLE_FORMATEUR` | + Voir/éditer Formations/Works, créer Works, voir historiques |
| `ROLE_ADMIN` | + Tout gérer (users, inscriptions, pages, messages, révisions) sauf supprimer |
| `ROLE_SUPER_ADMIN` | Accès total, suppression, gestion des super admins |
