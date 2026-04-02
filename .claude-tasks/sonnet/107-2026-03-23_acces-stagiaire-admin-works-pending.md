# 107 — Accès ROLE_STAGIAIRE à l'admin Works avec workflow PENDING

**Modèle** : Sonnet
**Justification** : Fonctionnalité multi-fichiers impliquant sécurité, service, repository et controllers.

## Fichiers modifiés
- `config/packages/security.yaml`
- `src/Repository/UserRepository.php`
- `src/Service/RevisionService.php`
- `src/Controller/Admin/DashboardController.php`
- `src/Controller/Admin/WorksCrudController.php`

## Résumé

Les stagiaires (`ROLE_STAGIAIRE`) peuvent désormais accéder à l'administration EasyAdmin
mais uniquement pour modifier les Works dont ils sont étudiants.
Leurs modifications créent une révision PENDING validée par un ROLE_FORMATEUR minimum.

### Détails par fichier

**security.yaml** : `^/admin` passe de `ROLE_FORMATEUR` à `ROLE_STAGIAIRE`

**UserRepository** : ajout de `findFormateurs()` (ROLE_FORMATEUR + ROLE_ADMIN + ROLE_SUPER_ADMIN)

**RevisionService** : ajout de `notifyFormateurs(Revision $revision)` — envoie l'email
de notification aux formateurs (via `findFormateurs()`) lors d'une révision soumise par un stagiaire

**DashboardController** :
- `#[IsGranted]` passe de `ROLE_FORMATEUR` à `ROLE_STAGIAIRE`
- Menu "Formations" restreint à `ROLE_FORMATEUR` (caché aux stagiaires)

**WorksCrudController** :
- `createIndexQueryBuilder()` : filtre la liste aux seuls Works de l'étudiant (JOIN sur `users`)
- `edit()` : vérifie que le stagiaire appartient au Works avant d'afficher le formulaire
- `configureActions()` : `Action::NEW` restreint à `ROLE_FORMATEUR`
- `updateEntity()` : si ROLE_STAGIAIRE → PENDING + notify formateurs ; sinon auto-approuvé

### Workflow stagiaire
1. Se connecte à `/admin` → voit uniquement ses Works
2. Modifie → révision PENDING créée, contenu live inchangé
3. Les formateurs reçoivent un email et peuvent approuver/rejeter via l'historique
