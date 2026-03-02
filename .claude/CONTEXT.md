# Contexte technique complet du projet

## Entités principales
- User (roles: ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_USER)
- Formation — relation ManyToOne avec User
- Works — relation ManyToOne avec Formation et ManyToMany avec User, permet de mettre des travaux d'élèves de la Formation, éventuellement des CV, gestion des fichiers uploadés
- Messages - Messages laissés par les utilisateurs sur les pages de Works, avec modération obligatoire
- Inscription — relation ManyToOne avec Formation, envoyé via le formulaire d'inscription si la formation est en recrutement
- ContactMessage — messages envoyés via le formulaire de contact
- Page — relation ManyToOne avec User, contenu modifiable (ex: mentions légales)
- Partenaire — gestion des partenaires du CF2m avec logo et description

## Services critiques
### Sécurité & Authentification
- `App\Service\AuthService`
  Gestion de la connexion, déconnexion, vérification des rôles.
  Dépend de : `Security`, `UserRepository`

- `App\Service\TokenService`
  Génération et validation des tokens (confirmation email, reset password).
  Durée de vie configurable via `services.yaml`.
  Dépend de : `UserRepository`, `MailerService`

- `App\Service\PasswordResetService`
  Orchestration du flux complet : demande → token → vérification → nouveau mot de passe.
  Dépend de : `TokenService`, `MailerService`, `UserRepository`

### Utilisateurs
- `App\Service\UserService`
  Création, modification, désactivation des comptes.
  Gère le hashage du mot de passe via `UserPasswordHasherInterface`.
  Dépend de : `UserRepository`, `EntityManagerInterface`

- `App\Service\ProfileService`
  Mise à jour des données de profil, upload d'avatar.
  Dépend de : `UserRepository`, `FileUploadService`

- `App\Service\RoleService`
  Attribution et révocation des rôles, vérification des permissions.
  Dépend de : `UserRepository`, `Security`


### Fichiers & Médias
- `App\Service\FileUploadService`
  Validation (type MIME, taille max), renommage sécurisé (UUID), déplacement vers `public/uploads/`.
  Paramètres : `upload_dir`, `allowed_mime_types`, `max_size` (dans `services.yaml`).
  Dépend de : `Filesystem` (Symfony)

- `App\Service\ImageResizeService`
  Redimensionnement et optimisation des images après upload (GD ou Intervention Image).
  Dépend de : `FileUploadService`

- `App\Service\FileDeleteService`
  Suppression physique du fichier + nettoyage des références en BDD.
  Dépend de : `Filesystem`, `EntityManagerInterface`

### Emails & Notifications
- `App\Service\MailerService`
  Envoi d'emails via Symfony Mailer avec templates Twig.
  Méthodes : `sendWelcome()`, `sendPasswordReset()`, `sendNotification()`.
  Dépend de : `MailerInterface`, `TwigEnvironment`

- `App\Service\NotificationService`
  Création et gestion des notifications en base (lues/non lues).
  Dépend de : `NotificationRepository`, `EntityManagerInterface`

- `App\Service\ContactService`
  Traitement des messages de contact : validation, stockage, notification admin.
  Dépend de : `ContactMessageRepository`, `MailerService`

- `App\Service\InscriptionService`
  Traitement des messages d'inscription : validation, stockage, notification admin.
  Dépend de : `InscriptionRepository`, `MailerService`

### Contenu
- `App\Service\PageService`
  Création, publication, dépublication, archivage des pages.
  Gère le calcul du `slug` (unique) et la date de publication.
  Dépend de : `PageRepository`, `SluggerInterface`


- `App\Service\FormationService`
  Création, publication, dépublication, archivage des Formations. On ne peut s'inscrire que sur les formations en recrutement.
  Gère le calcul du `slug` (unique) et la date de publication.
  Dépend de : `FormationRepository`, `SluggerInterface`

- `App\Service\WorksService`
  Création, publication, dépublication, archivage des travaux de stagiaires pour les Formations. On ne peut s'inscrire que sur les formations en recrutement.
  Gère le calcul du `slug` (unique) et la date de publication.
  Dépend de : `FormationRepository`,`WorksRepository`, `SluggerInterface`

- `App\Service\SlugService`
  Génération de slugs uniques avec gestion des doublons (ajout d'un suffixe `-2`, `-3`...).
  Dépend de : `SluggerInterface`, `PageRepository`,`FormationRepository`

- `App\Service\SearchService`
  Recherche full-text via `LIKE` ou intégration Meilisearch/Elasticsearch.
  Dépend de : `PageRepository`, `FormationRepository`, `WorksRepository`, `EntityManagerInterface`,

### Cache & Performance
- `App\Service\CacheService`
  Abstraction du cache Symfony (PSR-6/PSR-16) pour les données coûteuses à calculer.
  Dépend de : `CacheInterface`

- `App\Service\StatisticsService`
  Calcul de statistiques (vues, inscriptions, ventes) avec mise en cache.
  Dépend de : `CacheService`, repositories divers

### Logging & Audit
- `App\Service\AuditService`
  Enregistrement des actions sensibles en BDD (qui a fait quoi, quand, depuis quelle IP).
  Dépend de : `AuditLogRepository`, `Security`, `RequestStack`

- `App\Service\ActivityLogService`
  Historique des actions utilisateur pour affichage dans le back-office.
  Dépend de : `ActivityLogRepository`, `EntityManagerInterface`

## Contraintes importantes
- PHP 8.5 strict_types partout
- Pas de JavaScript bundler (ImportMap natif Symfony)
- Toutes les routes API préfixées `/api/`
- Multilingue : base de Symfony reste en `en`, mais le site sera actuellement en `fr` uniquement pour l'instant

## Variables d'environnement attendues
DATABASE_URL, MAILER_DSN, APP_SECRET, APP_ENV
