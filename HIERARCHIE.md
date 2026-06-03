# Hiérarchie des rôles — CF2m

> Dernière mise à jour : 2026-04-12

---

## Hiérarchie des rôles

```
ROLE_SUPER_ADMIN
    └── ROLE_ADMIN
        │    └── ROLE_FORMATEUR
        │            └── ROLE_STAGIAIRE
        │                    └── ROLE_USER
        │
        ROLE_PEDAGO (parallèle, indépendant de ROLE_ADMIN)
        
```

> `ROLE_PEDAGO` hérite de `ROLE_FORMATEUR`, `ROLE_STAGIAIRE` et `ROLE_USER` mais **pas** de `ROLE_ADMIN`.
> Un utilisateur peut cumuler `ROLE_ADMIN` + `ROLE_PEDAGO` — dans ce cas les droits `ROLE_ADMIN` priment.

---

## Accès global (security.yaml)

| Zone        | Rôle minimum requis |
|-------------|---------------------|
| `/admin/*`  | `ROLE_STAGIAIRE`    |
| `/profil/*` | `ROLE_USER`         |
| Site public | Anonyme             |

---

## Admin EasyAdmin — menu et permissions

### Section Contenu — Formations

| Action                       | `ROLE_FORMATEUR` |   `ROLE_PEDAGO`   |   `ROLE_ADMIN`    | `ROLE_SUPER_ADMIN` |
|------------------------------|:----------------:|:-----------------:|:-----------------:|:------------------:|
| Voir la liste                |        ✅         |         ✅         |         ✅         |         ✅          |
| Créer                        |        ❌         | ✅ (auto-approuvé) | ✅ (auto-approuvé) |         ✅          |
| Éditer (si responsable)      |       ✅ ¹        | ✅ (auto-approuvé) | ✅ (auto-approuvé) |         ✅          |
| Approuver / Rejeter révision |        ❌         |         ✅         |         ✅         |         ✅          |
| Restaurer une version        |        ❌         |         ✅         |         ✅         |         ✅          |
| Supprimer                    |        ❌         |         ❌         |         ❌         |         ✅          |
| Voir l'historique            |        ✅         |         ✅         |         ✅         |         ✅          |

> ¹ Formateur peut éditer uniquement s'il est responsable de la formation ; la révision passe en statut PENDING.

### Section Contenu — Works

| Action                       | `ROLE_STAGIAIRE` | `ROLE_FORMATEUR` |   `ROLE_PEDAGO`   | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` |
|------------------------------|:----------------:|:----------------:|:-----------------:|:------------:|:------------------:|
| Voir la liste                |       ✅ ²        |        ✅         | ✅ (lecture seule) |      ✅       |         ✅          |
| Créer                        |        ❌         |        ✅         |         ❌         |      ✅       |         ✅          |
| Éditer                       |       ✅ ³        |       ✅ ⁴        |         ❌         |      ✅       |         ✅          |
| Approuver / Rejeter révision |        ❌         |       ✅ ⁴        |         ❌         |      ✅       |         ✅          |
| Restaurer une version        |        ❌         |       ✅ ⁴        |         ❌         |      ✅       |         ✅          |
| Supprimer                    |        ❌         |        ❌         |         ❌         |      ❌       |         ✅          |
| Voir l'historique            |        ❌         |        ✅         |         ✅         |      ✅       |         ✅          |

> ² Stagiaire ne voit que les Works auxquels il appartient.
> ³ Stagiaire ne peut éditer que les Works dont il fait partie.
> ⁴ Formateur uniquement s'il est responsable de la formation parente du Works.

### Section Contenu — Pages

| Action                       | `ROLE_PEDAGO` | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` |
|------------------------------|:-------------:|:------------:|:------------------:|
| Voir / Éditer                |       ✅       |      ✅       |         ✅          |
| Approuver / Rejeter révision |       ✅       |      ✅       |         ✅          |
| Restaurer une version        |       ✅       |      ✅       |         ✅          |
| Supprimer                    |       ❌       |      ❌       |         ✅          |

### Section Utilisateurs

| Action                                                        | `ROLE_PEDAGO` | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` |
|---------------------------------------------------------------|:-------------:|:------------:|:------------------:|
| Voir / Créer / Éditer                                         |      ✅ ⁵      |      ✅       |         ✅          |
| Attribuer `ROLE_STAGIAIRE` / `ROLE_FORMATEUR` / `ROLE_PEDAGO` |       ✅       |      ✅       |         ✅          |
| Attribuer `ROLE_ADMIN`                                        |       ❌       |      ✅       |         ✅          |
| Attribuer `ROLE_SUPER_ADMIN`                                  |       ❌       |      ❌       |         ✅          |
| Voir / Éditer un `ROLE_SUPER_ADMIN`                           |       ❌       |      ❌       |         ✅          |
| Supprimer                                                     |       ❌       |      ❌       |         ✅          |

> ⁵ `ROLE_PEDAGO` sans `ROLE_ADMIN` ne peut attribuer que `ROLE_STAGIAIRE`, `ROLE_FORMATEUR` ou `ROLE_PEDAGO`.
> Le cumul `ROLE_ADMIN` + `ROLE_PEDAGO` donne les droits complets de `ROLE_ADMIN`.

### Section Inscriptions

| Action         | `ROLE_PEDAGO` | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` |
|----------------|:-------------:|:------------:|:------------------:|
| Voir / Traiter |       ✅       |      ✅       |         ✅          |
| Supprimer      |       ❌       |      ❌       |         ✅          |

### Section Communication — Messages de contact

| Action       | `ROLE_PEDAGO` | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` |
|--------------|:-------------:|:------------:|:------------------:|
| Voir / Gérer |       ✅       |      ✅       |         ✅          |
| Supprimer    |       ❌       |      ❌       |         ✅          |

### Section Communication — Partenaires

| Action                | `ROLE_ADMIN` | `ROLE_SUPER_ADMIN` |
|-----------------------|:------------:|:------------------:|
| Voir / Créer / Éditer |      ✅       |         ✅          |
| Supprimer             |      ❌       |         ✅          |

---

## Mails envoyés par action

### Création de compte utilisateur (admin)

| Déclencheur                             | Destinataire     | Template                                                 |
|-----------------------------------------|------------------|----------------------------------------------------------|
| Création d'un utilisateur via EasyAdmin | Utilisateur créé | `emails/user_bienvenue.html.twig` (identifiants générés) |

### Profil utilisateur

| Déclencheur                                 | Destinataire         | Template            |
|---------------------------------------------|----------------------|---------------------|
| Demande de réinitialisation de mot de passe | Utilisateur concerné | Email lien de reset |

### Préinscription (formulaire public)

| Déclencheur                     | Destinataires                                     | Template                             |
|---------------------------------|---------------------------------------------------|--------------------------------------|
| Soumission d'une préinscription | `ROLE_ADMIN` + `ROLE_SUPER_ADMIN` + `ROLE_PEDAGO` | `emails/inscription_admin.html.twig` |
| Soumission d'une préinscription | Candidat (accusé de réception)                    | `emails/inscription_ar.html.twig`    |

### Formulaire de contact (public)

| Déclencheur                        | Destinataires                                     | Template            |
|------------------------------------|---------------------------------------------------|---------------------|
| Soumission d'un message de contact | Adresse admin (mailFrom)                          | Email contact       |
| Soumission d'un message de contact | `ROLE_ADMIN` + `ROLE_SUPER_ADMIN` + `ROLE_PEDAGO` | Copie email contact |

> `ROLE_PEDAGO` reçoit les mails de préinscription et de contact, **pas** les mails de révision.

### Révisions — Formations

| Déclencheur                                  | Destinataires                     | Template                             |
|----------------------------------------------|-----------------------------------|--------------------------------------|
| Révision PENDING créée (formateur non-admin) | `ROLE_ADMIN` + `ROLE_SUPER_ADMIN` | `emails/revision_pending.html.twig`  |
| Révision approuvée                           | Auteur de la révision             | `emails/revision_approved.html.twig` |
| Révision rejetée                             | Auteur de la révision             | `emails/revision_rejected.html.twig` |

### Révisions — Pages

| Déclencheur            | Destinataires                     | Template                             |
|------------------------|-----------------------------------|--------------------------------------|
| Révision PENDING créée | `ROLE_ADMIN` + `ROLE_SUPER_ADMIN` | `emails/revision_pending.html.twig`  |
| Révision approuvée     | Auteur de la révision             | `emails/revision_approved.html.twig` |
| Révision rejetée       | Auteur de la révision             | `emails/revision_rejected.html.twig` |

### Révisions — Works

| Déclencheur                        | Destinataires                                   | Template                             |
|------------------------------------|-------------------------------------------------|--------------------------------------|
| Révision PENDING créée (stagiaire) | Formateurs responsables de la formation parente | `emails/works_pending.html.twig`     |
| Révision approuvée                 | Auteur de la révision                           | `emails/revision_approved.html.twig` |
| Révision rejetée                   | Auteur de la révision                           | `emails/revision_rejected.html.twig` |

> Les révisions auto-approuvées (`ROLE_ADMIN`, `ROLE_PEDAGO`) ne génèrent **aucun mail**.

---

## Résumé par rôle

| Rôle               | Ce qu'il peut faire                                                                                                                         |
|--------------------|---------------------------------------------------------------------------------------------------------------------------------------------|
| `ROLE_USER`        | Accès profil uniquement, site public                                                                                                        |
| `ROLE_STAGIAIRE`   | + Admin (lecture commentaires, ses propres Works, édition partielle)                                                                        |
| `ROLE_FORMATEUR`   | + Voir/éditer Formations et Works (si responsable), créer Works, voir historiques                                                           |
| `ROLE_PEDAGO`      | + Créer/éditer Formations (auto-approve), gérer Pages/Inscriptions/Users/Contact — Works en lecture seule — **ne dépend pas de ROLE_ADMIN** |
| `ROLE_ADMIN`       | + Tout gérer (users, inscriptions, pages, messages, révisions) sauf supprimer                                                               |
| `ROLE_SUPER_ADMIN` | Accès total, suppression, gestion des super admins                                                                                          |
