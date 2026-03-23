# 092 — Accès ROLE_STAGIAIRE à l'admin Works avec workflow PENDING

**Date** : 2026-03-23 14:00
**Branche** : main

## Fichiers modifiés
- `config/packages/security.yaml`
- `src/Repository/UserRepository.php`
- `src/Service/RevisionService.php`
- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/WorksCrudController.php`

## Changements

### `security.yaml`
`access_control` : `^/admin` passe de `ROLE_FORMATEUR` à `ROLE_STAGIAIRE`.

### `UserRepository`
Nouvelle méthode `findFormateurs()` : retourne tous les utilisateurs ayant `ROLE_FORMATEUR`, `ROLE_ADMIN` ou `ROLE_SUPER_ADMIN` (requête LIKE sur le champ JSON `roles`).

### `RevisionService`
Nouvelle méthode publique `notifyFormateurs(Revision $revision)` : envoie l'email de notification de révision en attente (`emails/revision_pending.html.twig`) à tous les utilisateurs retournés par `UserRepository::findFormateurs()`.

### `DashboardController`
- `#[IsGranted('ROLE_FORMATEUR')]` → `#[IsGranted('ROLE_STAGIAIRE')]`
- Menu "Formations" : `->setPermission('ROLE_FORMATEUR')` ajouté — caché aux stagiaires
- Les autres entrées de menu restent inchangées (déjà protégées par `ROLE_ADMIN`)

### `WorksCrudController`

#### Imports ajoutés
`Doctrine\ORM\QueryBuilder`, `EasyCorp\...\Collection\FieldCollection`, `EasyCorp\...\Collection\FilterCollection`, `EasyCorp\...\Config\KeyValueStore`, `EasyCorp\...\Dto\EntityDto`, `EasyCorp\...\Dto\SearchDto`

#### `configureActions()`
`Action::NEW` restreint à `ROLE_FORMATEUR` : les stagiaires ne peuvent pas créer de Works.

#### `createIndexQueryBuilder()`
Nouveau — filtre la liste pour les stagiaires : `JOIN entity.users` avec `WHERE u.id = :currentUserId`. Un stagiaire ne voit que les Works dont il fait partie.

#### `edit()`
Nouveau — vérifie que le stagiaire appartient au Works avant d'afficher le formulaire. Lève un 403 sinon.

#### `updateEntity()`
Workflow conditionnel :

| Rôle | Comportement |
|------|-------------|
| `ROLE_STAGIAIRE` | Révision `STATUS_PENDING` créée, contenu live inchangé, email aux formateurs via `notifyFormateurs()` |
| `ROLE_STAGIAIRE` (révision PENDING déjà existante) | Mise à jour de la révision PENDING existante via `updatePendingTypedHistory()` |
| `ROLE_FORMATEUR`+ | Révision `STATUS_AUTO_APPROVED`, contenu live mis à jour (comportement inchangé) |

Double sécurité en `updateEntity()` : vérification que le stagiaire appartient bien au Works, même si la requête HTTP est manipulée.

## Workflow complet stagiaire
1. Se connecte à `/admin` → tableau de bord réduit (Works uniquement dans le menu Contenu)
2. Voit dans la liste uniquement les Works dont il est étudiant
3. Clique sur Modifier → formulaire standard (sans bouton Nouveau, sans Historique)
4. Sauvegarde → révision `PENDING` créée, contenu live inchangé, flash d'avertissement affiché
5. Les formateurs reçoivent un email et peuvent approuver/rejeter via la page d'historique du Works

## Raison
Les stagiaires doivent pouvoir contribuer à leurs Works sans avoir la main directe sur la publication. Un formateur valide chaque modification, garantissant la qualité du contenu.
