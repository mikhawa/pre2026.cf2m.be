# 083 — Mise à jour de la révision PENDING existante pour ROLE_FORMATEUR

**Modèle** : Sonnet
**Justification** : Logique métier de workflow révisions

## Fonctionnalité implémentée
Quand un formateur modifie une formation déjà en attente de validation :
- Au lieu de créer une nouvelle révision PENDING, le système met à jour la révision existante
- La table `formation` reste inchangée (partie publique non affectée)
- Les admins ne sont pas re-notifiés (évite le spam)
- Un badge "En attente" apparaît dans la liste des formations

## Fichiers modifiés
- `src/Repository/RevisionRepository.php` — ajout `findPendingForEntity(string, int): ?Revision`
- `src/Service/RevisionService.php`
  - ajout `updatePendingRevision(Revision, object): void`
  - ajout `applyRevisionDataToFormation(Formation, array): void` (inject en mémoire, sans flush)
- `src/Entity/Formation.php` — ajout getter virtuel `getRevisionPendante(): ?string` (pour EasyAdmin)
- `src/Controller/Admin/FormationCrudController.php`
  - `updateEntity()` : branche selon existence révision PENDING (update vs create)
  - `configureFields()` : badge "En attente" dans la liste (colonne virtuelle)
  - `edit()` surchargé : injecte les données de la révision PENDING dans le formulaire pour que le formateur voie ses modifications en attente + flash info sur GET

## Workflow résultant
1. Formateur modifie formation → révision PENDING créée, admins notifiés, Formation inchangée
2. Formateur modifie à nouveau (révision toujours PENDING) → révision PENDING mise à jour, pas de nouvelle notif
3. Admin approuve → révision APPROVED, Formation mise à jour en live
4. Admin rejette → révision REJECTED, prochaine modif du formateur créera une nouvelle révision PENDING

## Design decisions
- `previousData` NON écrasé lors de l'update : conserve l'état original avant la première soumission
- Badge visible uniquement dans la liste (index) via `formatValue` + getter virtuel nul
- Pas de modification du schéma BDD
