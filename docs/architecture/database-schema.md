# Schéma de base de données — CF2m

## Moteur
MariaDB 11.4 — encodage `utf8mb4` — collation `utf8mb4_unicode_ci`

## Entités principales

### User
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | Auto-increment |
| email | varchar(180) | Unique |
| roles | json | Ex: ["ROLE_ADMIN"] |
| password | varchar(255) | Hashé (bcrypt) |
| is_verified | bool | Confirmation email |
| created_at | datetime | |
| updated_at | datetime | |
| avatar | varchar(255) | Nullable — chemin relatif |

### Formation
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| title | varchar(255) | |
| slug | varchar(255) | Unique |
| description | longtext | |
| status | varchar(20) | draft / published / archived / recruiting |
| created_at | datetime | |
| published_at | datetime | Nullable |
| user_id | int (FK → User) | Formateur responsable |

### Works (travaux de stagiaires)
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| title | varchar(255) | |
| slug | varchar(255) | Unique |
| description | longtext | Nullable |
| status | varchar(20) | draft / published / archived |
| formation_id | int (FK → Formation) | ManyToOne |
| created_at | datetime | |
| published_at | datetime | Nullable |

**Relation** : `works_user` (ManyToMany entre Works et User)

### Messages
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| content | longtext | |
| is_approved | bool | Modération obligatoire |
| created_at | datetime | |
| user_id | int (FK → User) | |
| works_id | int (FK → Works) | |

### Inscription
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| nom | varchar(100) | |
| prenom | varchar(100) | |
| email | varchar(180) | |
| message | longtext | Nullable |
| created_at | datetime | |
| formation_id | int (FK → Formation) | ManyToOne |

### ContactMessage
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| nom | varchar(100) | |
| email | varchar(180) | |
| sujet | varchar(255) | |
| message | longtext | |
| created_at | datetime | |
| is_read | bool | |

### Page
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| title | varchar(255) | |
| slug | varchar(255) | Unique |
| content | longtext | |
| status | varchar(20) | draft / published / archived |
| created_at | datetime | |
| published_at | datetime | Nullable |
| user_id | int (FK → User) | Auteur |

### Partenaire
| Champ | Type | Notes |
|-------|------|-------|
| id | int (PK) | |
| nom | varchar(255) | |
| description | longtext | Nullable |
| logo | varchar(255) | Nullable — chemin relatif |
| url | varchar(255) | Nullable |
| is_active | bool | |

## Relations résumées
```
User ──< Formation         (ManyToOne)
Formation ──< Works        (ManyToOne)
Works >──< User            (ManyToMany via works_user)
Works ──< Messages         (ManyToOne)
User ──< Messages          (ManyToOne)
Formation ──< Inscription  (ManyToOne)
User ──< Page              (ManyToOne)
```

## Conventions
- Nommage BDD : `snake_case`
- Clés étrangères : suffixe `_id`
- Tables de jointure ManyToMany : `entite1_entite2` (ordre alphabétique)
- Statuts gérés comme `string` enum-like (pas d'enum MySQL pour portabilité)
