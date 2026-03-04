# Schéma de base de données — CF2m

## Moteur
MariaDB 11.4 — encodage `utf8mb4` — collation `utf8mb4_unicode_ci`

## Entités principales

### User
| Champ                       | Type              | Notes                                                           |
|-----------------------------|-------------------|-----------------------------------------------------------------|
| id                          | int unsigned (PK) | Auto-increment, assigné par Doctrine                            |
| email                       | varchar(180)      | Unique                                                          |
| roles                       | json              | Ex: ["ROLE_ADMIN"] — ROLE_USER toujours ajouté                  |
| password                    | varchar(255)      | Hashé (bcrypt) — non mappé : `plainPassword`                    |
| user_name                   | varchar(50)       | Unique, alphanumérique + underscore                             |
| activation_token            | varchar(64)       | Nullable — confirmation email                                   |
| status                      | smallint unsigned | 0 = inactif, 1 = actif, 2 = banni (défaut : 0)                  |
| reset_password_token        | varchar(64)       | Nullable                                                        |
| reset_password_requested_at | datetime          | Nullable                                                        |
| avatar_name                 | varchar(255)      | Nullable — nom du fichier (VichUploader, mapping `user_avatar`) |
| biography                   | varchar(600)      | Nullable                                                        |
| external_link1              | varchar(255)      | Nullable — URL validée                                          |
| external_link2              | varchar(255)      | Nullable — URL validée                                          |
| external_link3              | varchar(255)      | Nullable — URL validée                                          |
| created_at                  | datetime          | Défini via `#[ORM\PrePersist]`                                  |
| updated_at                  | datetime          | Nullable — mis à jour via `setAvatarFile()` (VichUploader)      |

**Champ non mappé** : `avatarFile` (`File`) — géré par VichUploader, ne persiste pas en BDD.

### Formation
> Une formation peut avoir plusieurs utilisateurs responsables (ManyToMany), et peut avoir plusieurs travaux associés (OneToMany).

| Champ               | Type                     | Notes                                     |
|---------------------|--------------------------|-------------------------------------------|
| id                  | int unsigned (PK)        |                                           |
| title               | varchar(255)             |                                           |
| slug                | varchar(255)             | Unique                                    |
| description         | longtext                 | Nullable                                  |
| status              | varchar(20)              | draft / published / archived / recruiting |
| created_at          | datetime                 | Via `#[ORM\PrePersist]`                   |
| created_by_user_id  | int unsigned (FK → User) | Créateur (ManyToOne, obligatoire)         |
| published_at        | datetime                 | Nullable                                  |
| updated_at          | datetime                 | Nullable                                  |
| updated_by_user_id  | int unsigned (FK → User) | Nullable (si jamais mis à jour)           |

**Relation** : `formation_user` (ManyToMany entre Formation et User — responsables)

### Works (travaux de stagiaires)
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| title | varchar(255) | |
| slug | varchar(255) | Unique |
| description | longtext | Nullable |
| status | varchar(20) | draft / published / archived |
| formation_id | int unsigned (FK → Formation) | ManyToOne |
| created_at | datetime | |
| published_at | datetime | Nullable |

**Relation** : `works_user` (ManyToMany entre Works et User)

### Comment
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| content | longtext | |
| is_approved | bool | Modération obligatoire |
| created_at | datetime | |
| user_id | int unsigned (FK → User) | |
| works_id | int unsigned (FK → Works) | |

### Inscription
| Champ             | Type                          | Notes                                 |
|-------------------|-------------------------------|---------------------------------------|
| id                | int unsigned (PK)             |                                       |
| nom               | varchar(100)                  |                                       |
| prenom            | varchar(100)                  |                                       |
| email             | varchar(180)                  |                                       |
| message           | longtext                      | Nullable                              |
| created_at        | datetime                      | Via `#[ORM\PrePersist]`               |
| treat             | bool                          | false (défaut) / true                 |
| treat_at          | datetime                      | Nullable                              |
| treat_by_user_id  | int unsigned (FK → User)      | Nullable (si inscription non traitée) |
| formation_id      | int unsigned (FK → Formation) | ManyToOne                             |

### ContactMessage
> mail + DB


| Champ           | Type | Notes                                     |
|-----------------|------|-------------------------------------------|
| id              | int unsigned (PK) |                                           |
| nom             | varchar(100) |                                           |
| email           | varchar(180) |                                           |
| sujet           | varchar(255) |                                           |
| message         | longtext |                                           |
| created_at      | datetime |                                           |
| is_read         | bool |     false (défaut) / true                                          |
| read_by_user_id | int unsigned (FK → User) | Nullable (si message n'est pas encore lu) |

### Page
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| title | varchar(255) | |
| slug | varchar(255) | Unique |
| content | longtext | |
| status | varchar(20) | draft / published / archived |
| created_at | datetime | |
| published_at | datetime | Nullable |

**Relation** : `page_user` (ManyToMany entre Page et User)

### Partenaire
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| nom | varchar(255) | |
| description | longtext | Nullable |
| logo | varchar(255) | Nullable — chemin relatif |
| url | varchar(255) | Nullable |
| is_active | bool | |



### Rating
> Entité à créer — référencée dans `User.php` (OneToMany)
> S'applique uniquement sur **Works** et **Comment** via des relations ManyToMany

| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| value | smallint unsigned | Note attribuée (ex: 1 à 5) — à définir |
| created_at | datetime | Via `#[ORM\PrePersist]` |
| user_id | int unsigned (FK → User) | ManyToOne, orphanRemoval — auteur de la note |

**Tables de jointure** :
- `comment_rating` (ManyToMany entre Comment et Rating)
- `formation_user` (ManyToMany entre Formation et User — responsables)
- `page_user` (ManyToMany entre Page et User)
- `rating_works` (ManyToMany entre Rating et Works)
- `works_user` (ManyToMany entre Works et User)

## Relations résumées
```
User ──< Formation            (ManyToOne — créateur via created_by_user_id)
Formation >──< User           (ManyToMany via formation_user — responsables)
Formation ──< Works           (ManyToOne)
Works >──< User               (ManyToMany via works_user)
Works ──< Comment             (ManyToOne)
User ──< Comment              (ManyToOne — auteur)
Comment >──< Rating           (ManyToMany via comment_rating)
Works >──< Rating             (ManyToMany via rating_works)
User ──< Rating               (ManyToOne — auteur de la note)
Formation ──< Inscription     (ManyToOne)
Inscription ──> User          (ManyToOne nullable — traité par un admin)
ContactMessage ──> User       (ManyToOne nullable — lu par un admin)
Page >──< User                (ManyToMany via page_user)
```

## Conventions
- Nommage BDD : `snake_case`
- Clés étrangères : suffixe `_id`
- Tables de jointure ManyToMany : `entite1_entite2` (ordre alphabétique)
- `id` toujours `unsigned`
- Statuts gérés comme `smallint unsigned` (User.status) ou `string` enum-like selon l'entité
- Timestamps : `createdAt` via `#[ORM\PrePersist]`, `updatedAt` mis à jour via VichUploader (`setAvatarFile()`) ou manuellement
- Champs fichier VichUploader : non mappés en BDD (`avatarFile`), seul le nom est persisté (`avatarName`)
