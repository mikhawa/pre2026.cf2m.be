# 104 — Navbar sticky, bannissement utilisateurs, upload logo partenaire, amélioration 2FA et mails

**Date** : 2026-04-22 → 2026-04-24  
**Branche** : main

## Fichiers créés

- `src/EventSubscriber/PartenaireLogoResizeSubscriber.php`
- `migrations/Version20260424044040.php`
- `public/uploads/partenaire-logos/` (répertoire)
- `tests/Entity/PartenaireTest.php`

## Fichiers modifiés

- `assets/styles/app.css`
- `config/packages/doctrine_migrations.yaml`
- `config/packages/vich_uploader.yaml`
- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/PartenaireCrudController.php`
- `src/Controller/Admin/UserCrudController.php`
- `src/Controller/ContactController.php`
- `src/Controller/ProfileController.php`
- `src/Controller/TwoFactorController.php`
- `src/Entity/Partenaire.php`
- `src/EventSubscriber/TwoFactorKernelSubscriber.php`
- `src/EventSubscriber/TwoFactorLoginSubscriber.php`
- `src/Repository/ContactMessageRepository.php`
- `src/Repository/UserRepository.php`
- `src/Security/UserChecker.php`
- `src/Security/Voter/ContentManagerVoter.php`
- `src/Security/Voter/FormationVoter.php`
- `src/Security/Voter/WorksVoter.php`
- `templates/emails/contact.html.twig`
- `templates/home/index.html.twig`
- `templates/profil/utilisateurs.html.twig`
- `templates/security/login.html.twig`
- `templates/security/reset_password.html.twig`
- `templates/security/two_factor.html.twig`
- `templates/registration/register.html.twig`
- `translations/messages.fr.yaml`

## Résumé des changements

### 1. Navbar sticky universelle (tâche #147)

La navbar était en `position: absolute` sur la home, login et register, ce qui la faisait disparaître au scroll. Elle est maintenant `position: sticky; top: 0; z-index: 1030` sur toutes les pages sans exception.

Les compensations de hauteur (`padding-top: 70px`, blocs CSS conditionnels) ont été supprimées dans les templates auth et dans `.cf2m-hero`. Le CSS est désormais une règle unique sur `.cf2m-navbar`.

### 2. Correction dépréciations Symfony Security (tâche #148)

Mise à jour des signatures de méthodes pour la compatibilité Symfony 8 :

- `UserChecker::checkPostAuth()` → ajout `?TokenInterface $token = null`
- `voteOnAttribute()` dans `ContentManagerVoter`, `FormationVoter`, `WorksVoter` → ajout `?Vote $vote = null`

Imports `Vote` (`Symfony\Component\Security\Core\Authorization\Voter\Vote`) et `TokenInterface` ajoutés.

### 3. Upload logo partenaire avec redimensionnement automatique (tâche #149)

Ajout d'un upload d'image pour les partenaires, avec redimensionnement automatique côté serveur :

- Mapping VichUploader `partenaire_logo` → `/uploads/partenaire-logos/`
- Entité `Partenaire` : ajout `#[Vich\Uploadable]`, champ `logoFile` (non mappé Doctrine), `logo` (nom fichier), `updatedAt`
- `PartenaireLogoResizeSubscriber` : écoute `vich_uploader.post_upload`, redimensionne via GD à 400×300 px max (ratio conservé, transparence PNG/GIF préservée)
- EasyAdmin : formulaire `VichImageType` + affichage `ImageField` en liste/détail
- Validation : max 2 Mo, formats JPEG/PNG/GIF/WebP/SVG
- Migration : ajout de la colonne `updated_at` sur la table `partenaire`

### 4. Affichage logo dans la section partenaires de l'accueil (tâche #150)

La carte partenaire affiche le logo s'il existe, sinon le nom en texte (fallback).

- Dark mode : logo rendu blanc (`filter: invert(1)`) avec teinte cyan au survol
- Light mode : couleurs naturelles
- Classe CSS `.cf2m-partner-logo` dans `app.css`

### 5. Correction dépréciation Doctrine Migrations transactional (tâche #151)

Ajout de `transactional: false` dans `config/packages/doctrine_migrations.yaml` pour éviter le conflit entre le wrapping transactionnel de Doctrine et les commits DDL implicites de MariaDB (notices en production lors de `ALTER TABLE` / `CREATE TABLE`).

### 6. Badge messages de contact non lus dans EasyAdmin (tâche #152)

Le menu gauche EasyAdmin affiche un badge rouge avec le nombre de messages de contact non lus (`isRead = false`).

- Nouvelle méthode `ContactMessageRepository::countUnread()`
- `DashboardController` : injection du repository, badge conditionnel sur l'item "Messages de contact"

### 7. Statut utilisateur "Banni" dans EasyAdmin (tâche #153)

Le champ `status` de l'entité `User` est maintenant affiché avec des libellés textuels et badges colorés dans EasyAdmin :

| Valeur | Libellé | Badge |
|--------|---------|-------|
| 0 | Non activé | gris |
| 1 | Activé | vert |
| 2 | Banni | rouge |

Remplacement du `IntegerField` brut par un `ChoiceField`. Filtre par statut ajouté en liste.

### 8. Blocage de connexion pour les utilisateurs bannis (tâche #154)

`UserChecker::checkPreAuth()` : un utilisateur avec `status = 2` ne peut plus se connecter. Message affiché :

> "Votre compte a été suspendu. Contactez l'administration pour plus d'informations."

Son contenu (works, commentaires, etc.) reste intact. Le super admin peut supprimer du contenu manuellement depuis EasyAdmin si nécessaire.

### 9. Lien "TRAITER" dans les mails de notification de contact (tâche #155)

Le mail envoyé lors d'un nouveau message de contact contient désormais un bouton "TRAITER" pointant directement vers la page de détail du message dans EasyAdmin.

- URL absolue générée via `AdminUrlGenerator` + `Request::getUriForPath()`
- `target="_blank" rel="noopener noreferrer"` pour compatibilité webmail (Gmail, etc.)

### 10. Mémorisation du chemin cible après double authentification (tâche #156)

Avant la redirection vers la page 2FA, l'URL initialement demandée (requêtes GET uniquement) est sauvegardée en session sous la clé `2fa_target_path`. Après validation du code 2FA, l'utilisateur est redirigé vers cette URL plutôt que vers son profil par défaut.

- `TwoFactorKernelSubscriber` : sauvegarde de `getRequestUri()` en session
- `TwoFactorController` : lecture + suppression de `2fa_target_path` après validation

### 11. Tous les membres visibles sur /profil/utilisateurs (tâche #157)

La page `/profil/utilisateurs` affiche désormais les membres avec tous les statuts (0, 1 et 2) au lieu des seuls membres actifs.

- `ProfileController` : `findAllOrderedByName()` remplace `findAllActiveOrderedByName()`
- Template `utilisateurs.html.twig` : badges "Non activé" (gris) et "Banni" (rouge) ajoutés pour les statuts 0 et 2

## Raison

Consolider l'expérience utilisateur (navbar cohérente, redirection 2FA intelligente), renforcer la gestion des membres problématiques (bannissement), enrichir l'interface d'administration (badge non lus, statuts lisibles, lien direct depuis les mails) et compléter la gestion des partenaires (logo uploadable avec redimensionnement automatique).
