# Schéma UML — Base de données pre2026.cf2m.be

> Généré le 2026-03-19 | Symfony 7.4 / Doctrine ORM / MariaDB 11.4
> Entités : 10 tables + 5 tables de jointure ManyToMany

---

## Diagramme entité-relation (Mermaid ERD)

```mermaid
erDiagram

    %% ─────────────────────────────────────────
    %% TABLE : user
    %% ─────────────────────────────────────────
    user {
        int_unsigned    id                          PK
        varchar_180     email                       "UQ, NN"
        json            roles                       "NN — ROLE_USER|FORMATEUR|ADMIN|SUPER_ADMIN"
        varchar_255     password                    "NN"
        varchar_50      user_name                   "UQ, NN"
        smallint_u      status                      "0=inactif / 1=actif"
        datetime        created_at                  "NN"
        varchar_64      activation_token            "nullable"
        varchar_64      reset_password_token        "nullable"
        datetime        reset_password_requested_at "nullable"
        varchar_255     avatar_name                 "nullable — VichUploader"
        varchar_600     biography                   "nullable"
        varchar_255     external_link1              "nullable"
        varchar_255     external_link2              "nullable"
        varchar_255     external_link3              "nullable"
        datetime        updated_at                  "nullable"
    }

    %% ─────────────────────────────────────────
    %% TABLE : formation
    %% ─────────────────────────────────────────
    formation {
        int_unsigned    id                PK
        varchar_255     title             "NN"
        varchar_255     slug              "UQ, NN"
        text            description       "nullable"
        varchar_20      status            "draft|published|archived|recruiting"
        varchar_7       color_primary     "nullable — hex #rrggbb"
        varchar_7       color_secondary   "nullable — hex #rrggbb"
        datetime        created_at        "NN"
        int_unsigned    created_by_id     FK
        datetime        published_at      "nullable"
        datetime        updated_at        "nullable"
        int_unsigned    updated_by_id     "FK nullable"
    }

    %% ─────────────────────────────────────────
    %% TABLE : works
    %% ─────────────────────────────────────────
    works {
        int_unsigned    id              PK
        varchar_255     title           "NN"
        varchar_255     slug            "UQ, NN"
        text            description     "nullable"
        varchar_20      status          "draft|published|archived"
        datetime        created_at      "NN"
        datetime        published_at    "nullable"
        int_unsigned    formation_id    FK
    }

    %% ─────────────────────────────────────────
    %% TABLE : page
    %% ─────────────────────────────────────────
    page {
        int_unsigned    id              PK
        varchar_255     title           "NN"
        varchar_255     slug            "UQ, NN"
        text            content         "NN — WYSIWYG"
        varchar_20      status          "draft|published|archived"
        datetime        created_at      "NN"
        datetime        published_at    "nullable"
    }

    %% ─────────────────────────────────────────
    %% TABLE : comment
    %% ─────────────────────────────────────────
    comment {
        int_unsigned    id              PK
        text            content         "NN"
        tinyint         is_approved     "0=non / 1=oui"
        datetime        created_at      "NN"
        int_unsigned    user_id         FK
        int_unsigned    works_id        FK
    }

    %% ─────────────────────────────────────────
    %% TABLE : rating
    %% ─────────────────────────────────────────
    rating {
        int_unsigned    id              PK
        smallint_u      value           "NN — 1 à 5"
        datetime        created_at      "NN"
        int_unsigned    user_id         FK
    }

    %% ─────────────────────────────────────────
    %% TABLE : inscription
    %% ─────────────────────────────────────────
    inscription {
        int_unsigned    id              PK
        varchar_100     nom             "NN"
        varchar_100     prenom          "NN"
        varchar_180     email           "NN"
        text            message         "nullable"
        datetime        created_at      "NN"
        tinyint         treat           "0=non traité / 1=traité"
        datetime        treat_at        "nullable"
        int_unsigned    formation_id    FK
        int_unsigned    treat_by_id     "FK nullable"
    }

    %% ─────────────────────────────────────────
    %% TABLE : revision
    %% ─────────────────────────────────────────
    revision {
        int_unsigned    id              PK
        varchar_20      entity_type     "NN — formation|works|page"
        int_unsigned    entity_id       "NN — ID dans la table cible"
        varchar_255     entity_title    "NN"
        json            data            "NN — snapshot proposé"
        json            previous_data   "nullable — état avant"
        smallint_u      status          "0=En attente / 1=Approuvée / 2=Rejetée"
        datetime        created_at      "NN"
        int_unsigned    created_by_id   FK
        text            review_note     "nullable"
        datetime        reviewed_at     "nullable"
        int_unsigned    reviewed_by_id  "FK nullable"
    }

    %% ─────────────────────────────────────────
    %% TABLE : partenaire
    %% ─────────────────────────────────────────
    partenaire {
        int_unsigned    id              PK
        varchar_255     nom             "NN"
        text            description     "nullable"
        varchar_255     logo            "nullable"
        varchar_255     url             "nullable"
        tinyint         is_active       "0=inactif / 1=actif"
    }

    %% ─────────────────────────────────────────
    %% TABLE : contact_message
    %% ─────────────────────────────────────────
    contact_message {
        int_unsigned    id              PK
        varchar_100     nom             "NN"
        varchar_180     email           "NN"
        varchar_255     sujet           "NN"
        text            message         "NN"
        datetime        created_at      "NN"
        tinyint         is_read         "0=non lu / 1=lu"
        int_unsigned    read_by_id      "FK nullable"
    }

    %% ─────────────────────────────────────────
    %% TABLES DE JOINTURE ManyToMany
    %% ─────────────────────────────────────────
    formation_user {
        int_unsigned    formation_id    FK
        int_unsigned    user_id         FK
    }

    works_user {
        int_unsigned    works_id        FK
        int_unsigned    user_id         FK
    }

    page_user {
        int_unsigned    page_id         FK
        int_unsigned    user_id         FK
    }

    comment_rating {
        int_unsigned    comment_id      FK
        int_unsigned    rating_id       FK
    }

    rating_works {
        int_unsigned    rating_id       FK
        int_unsigned    works_id        FK
    }

    %% ─────────────────────────────────────────
    %% RELATIONS
    %% ─────────────────────────────────────────

    %% Formation ←→ User (createdBy / updatedBy)
    user ||--o{ formation : "créateur (created_by_id)"
    user |o--o{ formation : "modificateur (updated_by_id)"

    %% Works → Formation
    formation ||--o{ works : "contient (formation_id)"

    %% Comment → User & Works
    user ||--o{ comment : "auteur (user_id)"
    works ||--o{ comment : "commente (works_id)"

    %% Rating → User
    user ||--o{ rating : "évalue (user_id)"

    %% Inscription → Formation & User
    formation ||--o{ inscription : "reçoit (formation_id)"
    user |o--o{ inscription : "traite (treat_by_id)"

    %% Revision → User
    user ||--o{ revision : "crée (created_by_id)"
    user |o--o{ revision : "relit (reviewed_by_id)"

    %% ContactMessage → User
    user |o--o{ contact_message : "lit (read_by_id)"

    %% ManyToMany via tables de jointure
    formation ||--o{ formation_user : ""
    formation_user }o--|| user : ""

    works ||--o{ works_user : ""
    works_user }o--|| user : ""

    page ||--o{ page_user : ""
    page_user }o--|| user : ""

    comment ||--o{ comment_rating : ""
    comment_rating }o--|| rating : ""

    rating ||--o{ rating_works : ""
    rating_works }o--|| works : ""
```

---

## Résumé des tables

| Table | Lignes de données | Description |
|---|---|---|
| `user` | — | Utilisateurs (étudiants, formateurs, admins) |
| `formation` | — | Formations proposées par le CF2m |
| `works` | — | Activités/projets rattachés à une formation |
| `page` | — | Pages CMS (contenu WYSIWYG) |
| `comment` | — | Commentaires sur les works |
| `rating` | — | Notes (1–5) attribuées à des works ou commentaires |
| `inscription` | — | Demandes d'inscription publiques |
| `revision` | — | Historique de révisions (workflow de validation) |
| `partenaire` | — | Partenaires du CF2m |
| `contact_message` | — | Messages du formulaire de contact |

### Tables de jointure ManyToMany

| Table jointure | Relation |
|---|---|
| `formation_user` | Formation ↔ User (responsables/formateurs) |
| `works_user` | Works ↔ User (co-auteurs) |
| `page_user` | Page ↔ User (co-auteurs) |
| `comment_rating` | Comment ↔ Rating |
| `rating_works` | Rating ↔ Works |

### Particularité : Révisions polymorphiques

La table `revision` utilise une relation polymorphique applicative :
- `entity_type` (varchar 20) indique la table cible : `"formation"`, `"works"` ou `"page"`
- `entity_id` pointe vers l'id dans cette table
- Pas de contrainte FK déclarée en base — gérée par `RevisionService`

Workflow : **En attente (0)** → **Approuvée (1)** ou **Rejetée (2)**
