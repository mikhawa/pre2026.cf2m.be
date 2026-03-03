# Back-office EasyAdmin 4 — CF2m

## Installation
```bash
composer require easycorp/easyadmin-bundle
```

## Accès
- URL : `/admin`
- Rôles autorisés : `ROLE_ADMIN`, `ROLE_SUPER_ADMIN`
- Configuration dans `config/packages/security.yaml` :
```yaml
access_control:
    - { path: ^/admin, roles: ROLE_ADMIN }
```

## Structure des fichiers
```
src/Controller/Admin/
├── DashboardController.php       # Point d'entrée principal du back-office
├── UserCrudController.php
├── FormationCrudController.php
├── WorksCrudController.php
├── MessagesCrudController.php
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
- Champs affichés : email, roles, is_verified, created_at
- Champ `password` exclu des formulaires (géré via `UserService`)
- Modification du rôle réservée à `ROLE_SUPER_ADMIN` (voter ou override de permission)

### FormationCrudController
- Champs : title, slug (auto-généré), description, status, published_at, user (formateur)
- Filtre par status (draft / published / archived / recruiting)
- Action rapide : publier / dépublier

### WorksCrudController
- Champs : title, slug, description, status, formation, published_at
- Relation ManyToMany avec User (liste des stagiaires associés)
- Gestion des fichiers uploadés via `FileUploadService`

### MessagesCrudController
- Champs : content, is_approved, created_at, user, works
- Action rapide : approuver / refuser un message (modération)
- Par défaut : filtre sur `is_approved = false` pour afficher les messages en attente

### InscriptionCrudController
- Champs : nom, prenom, email, message, formation, created_at
- Lecture seule (pas de création depuis le back-office)
- Export CSV à prévoir

### ContactMessageCrudController
- Champs : nom, email, sujet, message, created_at, is_read
- Action rapide : marquer comme lu
- Lecture seule

### PageCrudController
- Champs : title, slug, content (éditeur riche), status, published_at, user
- Éditeur WYSIWYG à configurer (ex: TipTap ou champ `TextareaField` + JS)

### PartenaireCrudController
- Champs : nom, description, logo (upload), url, is_active
- Upload du logo via `FileUploadService`

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
| Action | ROLE_ADMIN | ROLE_SUPER_ADMIN |
|--------|-----------|-----------------|
| Voir tous les CRUD | ✅ | ✅ |
| Modifier les rôles User | ❌ | ✅ |
| Supprimer un User | ❌ | ✅ |
| Accès aux logs d'audit | ❌ | ✅ |

### Champs communs à configurer
- `DateTimeField` : format `d/m/Y H:i` (locale fr)
- `SlugField` : auto-généré depuis le titre (via `SlugService`)
- `ImageField` : upload vers `public/uploads/` (via `FileUploadService`)

## TODO
- [ ] Implémenter `DashboardController` avec le menu complet
- [ ] Créer chaque `CrudController` et configurer `configureFields()`
- [ ] Restreindre les actions sensibles via `configureActions()` selon les rôles
- [ ] Intégrer un éditeur WYSIWYG pour les champs `content` (Page, Formation)
- [ ] Ajouter des filtres et tris personnalisés sur les listes
- [ ] Prévoir l'export CSV pour Inscription et ContactMessage
- [ ] Internationaliser les labels en français (`labels` dans chaque `CrudController`)
