# Back-office EasyAdmin 4 — CF2m

## Installation
```bash
composer require easycorp/easyadmin-bundle
composer require vich/uploader-bundle
```

SunEditor est intégré via ImportMap (pas de bundler) :
```bash
php bin/console importmap:require suneditor
```

## Accès
- URL : `/admin`
- Rôles autorisés : `ROLE_ADMIN`, `ROLE_SUPER_ADMIN`, `ROLE_FORMATEUR`
- Configuration dans `config/packages/security.yaml`

## Structure des fichiers
```
src/Controller/Admin/
├── DashboardController.php       # Point d'entrée principal du back-office
├── UserCrudController.php
├── FormationCrudController.php
├── WorksCrudController.php
├── CommentCrudController.php    
├── RatingCrudController.php
├── InscriptionCrudController.php
├── ContactMessageCrudController.php
├── PageCrudController.php
└── PartenaireCrudController.php

```

## DashboardController
Fichier : `src/Controller/Admin/DashboardController.php`

Responsabilités :
- Définir le titre et le logo du back-office
- Lister les entrées du menu latéral (`configureMenuItems()`)
- Configurer les assets (`configureAssets()`)
- Définir la page d'accueil du dashboard (`configureDashboard()`)

## CrudControllers par entité

### UserCrudController
- Champs affichés : email, roles, is_verified, created_at, **status**
- Champ `password` exclu des formulaires (géré via `UserService`)
- Modification du rôle réservée à `ROLE_SUPER_ADMIN` (voter ou override de permission)
- Champ `status` affiché en `ChoiceField` avec badges colorés :

| Valeur | Libellé | Badge |
|--------|---------|-------|
| 0 | Non activé | gris |
| 1 | Activé | vert |
| 2 | Banni | rouge |

- Filtre par statut disponible en liste
- Un utilisateur banni (status = 2) ne peut plus se connecter (`UserChecker::checkPreAuth()`)
- Le contenu d'un banni reste intact — suppression manuelle depuis EasyAdmin si nécessaire

### FormationCrudController
- Champs : title, slug (auto-généré), description, status, published_at, user (formateur)
- Champ `description` : `TextareaField` + SunEditor (avec upload d'images)
- Filtre par status (draft / published / archived / recruiting)
- Action rapide : publier / dépublier

### WorksCrudController
- Champs : title, slug, description, status, formation, published_at
- Relation ManyToMany avec User (liste des stagiaires associés)
- Champ `description` : `TextareaField` + SunEditor (éditeur riche avec upload de fichiers)
- Fichiers joints (CV, travaux) : upload via VichUploader

### CommentCrudController
- Champs : content, is_approved, created_at, user, works
- Action rapide : approuver / refuser un message (modération)
- Par défaut : filtre sur `is_approved = false` pour afficher les messages en attente

### RatingCrudController
- Champs : value (1-5), created_at, user_id
- Relation ManyToMany avec Works et Comment (via les tables de jointure)
- Lecture seule (pas de création depuis le back-office)
- Affiche des étoiles graphiques dans la liste (via un custom template)

### InscriptionCrudController
- Champs : nom, prenom, email, message, formation, created_at
- Lecture seule (pas de création depuis le back-office)
- Export CSV à prévoir
- Filtre par formation et date d'inscription
- Envoi d'email de confirmation à l'inscription (via `InscriptionService`)
- Envoi d'email de notification aux utilisateurs choisis via la formation (via `NotificationService`)

### ContactMessageCrudController
- Champs : nom, email, sujet, message, created_at, is_read
- Action rapide : marquer comme lu
- Lecture seule
- Envoi d'email de notification à l'équipe admin à la réception d'un message (via `ContactMessageService`)
- **Badge rouge** dans le menu latéral affichant le nombre de messages non lus (`isRead = false`)
  - Méthode : `ContactMessageRepository::countUnread()`
  - Injection du repository dans `DashboardController`, badge conditionnel sur l'item "Messages de contact"
- L'email de notification inclut un bouton **"TRAITER"** pointant directement vers le détail du message dans EasyAdmin (URL absolue via `AdminUrlGenerator`)

### PageCrudController
- Champs : title, slug, content, status, published_at, user
- Champ `content` : `TextareaField` + SunEditor (avec upload d'images et de fichiers)

### PartenaireCrudController
- Champs : nom, description, logo, url, is_active
- Champ `description` : `TextareaField` + SunEditor
- Champ `logo` : upload via `VichImageType` (formulaire) + `ImageField` (liste/détail)
  - Mapping VichUploader : `partenaire_logo` → `/uploads/partenaire-logos/`
  - Entité : `#[Vich\Uploadable]`, champ `logoFile` (non mappé Doctrine), `logo` (nom fichier), `updatedAt`
  - Redimensionnement automatique : `PartenaireLogoResizeSubscriber` écoute `vich_uploader.post_upload`, redimensionne via GD à 400×300 px max (ratio conservé, transparence PNG/GIF préservée)
  - Validation : max 2 Mo, formats JPEG/PNG/GIF/WebP/SVG
  - Fallback affichage accueil : si pas de logo → nom en texte ; dark mode → `filter: invert(1)` avec teinte cyan au survol

## Personnalisations globales

### Thème et langue
```php
// DashboardController.php
public function configureDashboard(): Dashboard
{
    return Dashboard::new()
        ->setTitle('CF2m Administration')
        ->setTranslationDomain('messages') // traductions en français
        ->renderContentMaximized();
}
```

### Permissions par rôle

| Action                         | ROLE_ADMIN | ROLE_SUPER_ADMIN | ROLE_FORMATEUR |
|--------------------------------|------------|------------------|----------------|
| Voir tous les CRUD             | ✅          | ✅                | ❌              |
| Modifier les rôles User        | ❌          | ✅                | ❌              |
| Supprimer un User              | ❌          | ✅                | ❌              |
| Accès aux logs d'audit         | ❌          | ✅                | ❌              |
 | Modifier les formations        | ✅          | ✅                | ✅              |
 | Créer une formation            | ❌          | ✅                | ❌              |
 | Créer et modifier les works    | ✅          | ✅                | ✅              |
| Modérer les commentaires       | ✅          | ✅                | ✅              |
| Gérer les inscriptions         | ✅          | ✅                | ❌              |
| Gérer les pages et partenaires | ✅          | ✅                | ❌              |

- Implémentation via `configureActions()` dans chaque `CrudController` et/ou via un

### Champs communs à configurer
- `DateTimeField` : format `d/m/Y H:i` (locale fr)
- `SlugField` : auto-généré depuis le titre (via `SlugService`)

### Éditeur riche — SunEditor
Utilisé pour tous les champs `TextareaField` de type contenu éditorial (Formation.description, Works.description, Page.content, Partenaire.description).

Intégration via un Stimulus controller qui cible les `textarea[data-controller="suneditor"]` :
- Toolbar complète (titres, gras, listes, tableaux, liens, médias)
- Upload d'images : endpoint dédié `/admin/upload/image` → stockage via VichUploader
- Upload de fichiers : endpoint dédié `/admin/upload/file` → stockage via VichUploader
- Langue : `fr` (fichier de langue SunEditor)

### Uploads & médias — VichUploader
`vich/uploader-bundle` remplace le `FileUploadService` custom pour tous les uploads du back-office.

Configuration (`config/packages/vich_uploader.yaml`) :
```yaml
vich_uploader:
    db_driver: orm
    mappings:
        formation_images:
            uri_prefix: /uploads/formations
            upload_destination: '%kernel.project_dir%/public/uploads/formations'
            namer: Vich\UploaderBundle\Naming\UniqidNamer
        works_files:
            uri_prefix: /uploads/works
            upload_destination: '%kernel.project_dir%/public/uploads/works'
            namer: Vich\UploaderBundle\Naming\UniqidNamer
        partenaire_logos:
            uri_prefix: /uploads/partenaires
            upload_destination: '%kernel.project_dir%/public/uploads/partenaires'
            namer: Vich\UploaderBundle\Naming\UniqidNamer
        user_avatars:
            uri_prefix: /uploads/avatars
            upload_destination: '%kernel.project_dir%/public/uploads/avatars'
            namer: Vich\UploaderBundle\Naming\UniqidNamer
```

Redimensionnement des images :
- Toujours effectué côté serveur après upload (via `ImageResizeService` utilisant GD ou Intervention Image)
- Tailles cibles à définir par mapping (ex: logos partenaires → 300×150px max)

## Configuration Doctrine Migrations

Fichier : `config/packages/doctrine_migrations.yaml`

Ajout de `transactional: false` pour éviter le conflit entre le wrapping transactionnel de Doctrine et les commits DDL implicites de MariaDB (notices en production lors de `ALTER TABLE` / `CREATE TABLE`).

---

## Mise à jour en temps réel — AJAX (inscriptions)

Implémenté le 2026-05-03 (branche `fix/07-update-details-admin`).

### Problème résolu

Dans la liste `/admin/inscription`, après basculement du toggle **"Traitée"**, trois éléments ne se mettaient pas à jour sans rechargement de page :
- colonne **"Traitée le"** de la ligne
- colonne **"Traitée par"** de la ligne
- **badge rouge** (compteur) sur l'entrée "Inscriptions" du menu latéral

### Endpoint AJAX — `InscriptionAjaxController`

Fichier : `src/Controller/Admin/InscriptionAjaxController.php`

```
GET /admin/inscription/{id}/traitement-info
```

Accès restreint à `ROLE_ADMIN`. Retourne :

```json
{
  "treatAt": "03/05/2026 14:30",
  "treatAtIso": "2026-05-03T14:30:00+02:00",
  "treatBy": "mikhawa",
  "untreatedCount": 3
}
```

### Module JS — `assets/inscription_treat.js`

Importé dans `assets/admin.js`. Fonctionne par **event delegation** sur `document` (survit aux navigations Turbo) :

- Écoute `change` sur tout `<input type="checkbox">` dans un `td[data-column="treat"]`
- Récupère l'ID depuis `<tr data-id="...">` (data attribute natif EasyAdmin 4)
- Attend **600 ms** pour laisser le PATCH EasyAdmin terminer côté serveur
- Appelle l'endpoint AJAX puis met à jour `td[data-column="treatAt"]` et `td[data-column="treatBy"]`
- Met à jour le badge via `#main-menu a.menu-item-contents[href*="/admin/inscription"] .menu-item-badge`

### Points techniques EasyAdmin 4

- **Liens du menu** : utilisent `/admin/inscription` (et non `?crudControllerFqcn=...`). Le sélecteur `href*="InscriptionCrudController"` ne fonctionne pas.
- **Structure du badge** : `<span class="menu-item-badge">` est enfant direct du `<a class="menu-item-contents">`, pas du `<span class="menu-item-label">`.

### Configuration des assets admin

`addHtmlContentToHead('<link rel="stylesheet" href="/assets/styles/admin.css">')` dans `DashboardController::configureAssets()` injectait un chemin non fingerprinted, introuvable avec les assets compilés.

**Solution retenue** :
- Supprimé `addHtmlContentToHead(...)` dans `DashboardController`
- Ajouté `import './styles/admin.css'` dans `assets/admin.js` → AssetMapper génère le `<link>` avec le hash correct

> **Note dev** : après toute modification JS/CSS, supprimer `public/assets/` et vider le cache (`php bin/console cache:clear`) pour forcer la recompilation.

## TODO
- [ ] Implémenter `DashboardController` avec le menu complet
- [ ] Créer chaque `CrudController` et configurer `configureFields()`
- [ ] Restreindre les actions sensibles via `configureActions()` selon les rôles
- [ ] Intégrer SunEditor via un Stimulus controller sur tous les `TextareaField` éditoriaux
- [ ] Créer les endpoints `/admin/upload/image` et `/admin/upload/file` pour SunEditor
- [ ] Configurer VichUploader avec les mappings par entité
- [ ] Brancher `ImageResizeService` sur les listeners VichUploader post-upload
- [ ] Ajouter des filtres et tris personnalisés sur les listes
- [ ] Prévoir l'export CSV pour Inscription et ContactMessage
- [ ] Internationaliser les labels en français (`labels` dans chaque `CrudController`)
