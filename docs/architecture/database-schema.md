# Schéma de base de données — CF2m

## Moteur
MariaDB 11.4 — encodage `utf8mb4` — collation `utf8mb4_unicode_ci`

## Entités principales

### User
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | Auto-increment, assigné par Doctrine |
| email | varchar(180) | Unique |
| roles | json | Ex: ["ROLE_ADMIN"] — ROLE_USER toujours ajouté |
| password | varchar(255) | Hashé (bcrypt) — non mappé : `plainPassword` |
| user_name | varchar(50) | Unique, alphanumérique + underscore |
| activation_token | varchar(64) | Nullable — confirmation email |
| status | smallint unsigned | 0 = inactif, 1 = actif (défaut : 0) |
| reset_password_token | varchar(64) | Nullable |
| reset_password_requested_at | datetime | Nullable |
| avatar_name | varchar(255) | Nullable — nom du fichier (VichUploader, mapping `user_avatar`) |
| biography | varchar(500) | Nullable |
| external_link1 | varchar(255) | Nullable — URL validée |
| external_link2 | varchar(255) | Nullable — URL validée |
| external_link3 | varchar(255) | Nullable — URL validée |
| created_at | datetime | Défini via `#[ORM\PrePersist]` |
| updated_at | datetime | Nullable — mis à jour via `setAvatarFile()` (VichUploader)

**Champ non mappé** : `avatarFile` (`File`) — géré par VichUploader, ne persiste pas en BDD.

### Formation
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| title | varchar(255) | |
| slug | varchar(255) | Unique |
| description | longtext | |
| status | varchar(20) | draft / published / archived / recruiting |
| created_at | datetime | |
| published_at | datetime | Nullable |
| user_id | int unsigned (FK → User) | Formateur responsable |

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

### Messages
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| content | longtext | |
| is_approved | bool | Modération obligatoire |
| created_at | datetime | |
| user_id | int unsigned (FK → User) | |
| works_id | int unsigned (FK → Works) | |

### Inscription
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| nom | varchar(100) | |
| prenom | varchar(100) | |
| email | varchar(180) | |
| message | longtext | Nullable |
| created_at | datetime | |
| formation_id | int unsigned (FK → Formation) | ManyToOne |

### ContactMessage
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| nom | varchar(100) | |
| email | varchar(180) | |
| sujet | varchar(255) | |
| message | longtext | |
| created_at | datetime | |
| is_read | bool | |

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
| user_id | int unsigned (FK → User) | Auteur |

### Partenaire
| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| nom | varchar(255) | |
| description | longtext | Nullable |
| logo | varchar(255) | Nullable — chemin relatif |
| url | varchar(255) | Nullable |
| is_active | bool | |

### Comment
> Entité à créer — référencée dans `User.php` (OneToMany)

| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| … | … | À définir |
| user_id | int unsigned (FK → User) | ManyToOne, orphanRemoval |

### Rating
> Entité à créer — référencée dans `User.php` (OneToMany)
> S'applique uniquement sur **Works** et **Messages** via des relations ManyToMany

| Champ | Type | Notes |
|-------|------|-------|
| id | int unsigned (PK) | |
| value | smallint unsigned | Note attribuée (ex: 1 à 5) — à définir |
| created_at | datetime | Via `#[ORM\PrePersist]` |
| user_id | int unsigned (FK → User) | ManyToOne, orphanRemoval — auteur de la note |

**Tables de jointure** :
- `rating_works` (ManyToMany entre Rating et Works)
- `rating_messages` (ManyToMany entre Rating et Messages)

## Relations résumées
```
User ──< Formation         (ManyToOne)
Formation ──< Works        (ManyToOne)
Works >──< User            (ManyToMany via works_user)
Works ──< Messages         (ManyToOne)
User ──< Messages          (ManyToOne)
Formation ──< Inscription  (ManyToOne)
User ──< Page              (ManyToOne)
User ──< Comment           (OneToMany, orphanRemoval)
User ──< Rating            (OneToMany, orphanRemoval)
Rating >──< Works          (ManyToMany via rating_works)
Rating >──< Messages       (ManyToMany via rating_messages)
```

## Conventions
- Nommage BDD : `snake_case`
- Clés étrangères : suffixe `_id`
- Tables de jointure ManyToMany : `entite1_entite2` (ordre alphabétique)
- `id` toujours `unsigned`
- Statuts gérés comme `smallint unsigned` (User.status) ou `string` enum-like selon l'entité
- Timestamps : `createdAt` via `#[ORM\PrePersist]`, `updatedAt` mis à jour via VichUploader (`setAvatarFile()`) ou manuellement
- Champs fichier VichUploader : non mappés en BDD (`avatarFile`), seul le nom est persisté (`avatarName`)
