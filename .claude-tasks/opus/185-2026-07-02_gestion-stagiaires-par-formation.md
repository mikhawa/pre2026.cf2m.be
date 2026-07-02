# Tâche : Gestion des stagiaires par formation (entité pivot FormationStagiaire)

**Numéro** : 185
**Date** : 2026-07-02
**Modèle utilisé** : Opus
**Justification du modèle** : Sécurité — design des voters (critère explicite de `.claude/models.md`). La tâche implique la conception d'un Voter contextuel (`FormationStagiaireVoter`), une stratégie de synchronisation de rôle physique impactant la sécurité globale (`ROLE_STAGIAIRE` / accès `/admin`), et l'intégration transverse (entité, service, migration, UI EasyAdmin).
**Complexité** : Complexe
**Fichiers concernés** :
- `src/Entity/FormationStagiaire.php` (créé)
- `src/Repository/FormationStagiaireRepository.php` (créé)
- `src/Service/StagiaireService.php` (créé)
- `src/Security/Voter/FormationStagiaireVoter.php` (créé)
- `tests/Security/Voter/FormationStagiaireVoterTest.php` (créé)
- `templates/admin/formation/stagiaires.html.twig` (créé)
- `migrations/Version20260702093000.php` (créé)
- `src/Entity/Formation.php` (modifié : relation OneToMany `stagiaires`)
- `src/Entity/User.php` (modifié : helpers `addRole()` / `removeRole()`)
- `src/Controller/Admin/FormationCrudController.php` (modifié : action + méthodes de gestion des stagiaires)

## Contexte nécessaire
Implémentation de l'**Option 2** de `docs/architecture/proposition-gestion-stagiaires-formation.md` : une entité pivot légère `FormationStagiaire` (avec traçabilité `addedBy`/`addedAt`) rattachant un `ROLE_USER` à une formation précise, gérée depuis EasyAdmin par un formateur responsable, un pédago ou un admin.

## Objectif
Permettre à un `ROLE_FORMATEUR` responsable (ou `ROLE_ADMIN`/`ROLE_PEDAGO` sur toute formation) d'ajouter/retirer des stagiaires d'une formation, avec synchronisation physique du rôle global `ROLE_STAGIAIRE` (stratégie A : le rôle est écrit/retiré en base selon la présence d'au moins une ligne `FormationStagiaire`).

## Contraintes
- Ne pas casser le filtre DQL existant `WorksCrudController` (`entity.roles LIKE '%ROLE_STAGIAIRE%'`) : la synchro physique le laisse fonctionner tel quel (vérifié, non modifié).
- `getRoles()` / `setRoles()` de `User` inchangés ; les helpers `addRole()`/`removeRole()` manipulent le tableau brut `$this->roles`.
- Voter contextuel toujours avec sujet `Formation` (pas de cas `CREATE` sans sujet).
- Action dédiée dans `FormationCrudController` (pattern `historiqueFormation`), pas de champ imbriqué.
- Migration écrite à la main (pas de `diff`), sans backfill des `ROLE_STAGIAIRE` existants.

## Critères d'acceptation
- [x] Entité pivot + contrainte unique `(formation, user)`
- [x] Repository avec `countForUser()` (comptage via requête) + `findForFormation()` trié
- [x] Service `ajouterStagiaire()` (idempotent) / `retirerStagiaire()` (retourne `true` si dernière formation)
- [x] `FormationStagiaireVoter` (`FORMATION_MANAGE_STAGIAIRES`) + tests unitaires
- [x] Action EasyAdmin + template (liste, formulaire d'ajout `<select>`, retrait avec confirmation JS)
- [x] Message flash explicite au retrait de la dernière formation (perte d'accès `/admin`)

## Résultat
Tous les livrables produits. Décisions notables :
- **`ajouterStagiaire()` idempotent** : retourne la ligne existante si l'utilisateur est déjà stagiaire (contrainte d'unicité en garde-fou base), simplifie le contrôleur.
- **Comptage via requête** (`countForUser`) après suppression pour fiabiliser la synchro du retrait de `ROLE_STAGIAIRE`.
- **FK `ON DELETE CASCADE`** sur `formation_id`/`user_id`, `ON DELETE SET NULL` sur `added_by_id` (préserve la ligne si le gestionnaire est supprimé).
- **UI** : `<select>` HTML simple (candidats = utilisateurs non déjà stagiaires) ; amélioration future possible = autocomplete Ajax (noté en commentaire, non implémenté).

Hors scope (reporté, cf. proposition §5.4/§5.5) : lien automatique `Inscription → stagiaire` et notifications e-mail.

### Vérifications exécutées
- `doctrine:schema:validate --skip-sync` → **[OK] mapping correct**
- `doctrine:migrations:migrate` → migration `Version20260702093000` appliquée
- `lint:container` → **[OK]** (autowiring `StagiaireService`, `FormationStagiaireRepository`)
- `cache:clear` → **[OK]**
- `phpunit tests/Security/Voter/` → **OK (81 tests, 87 assertions)**, aucune régression
- `php-cs-fixer` → 1 normalisation cosmétique appliquée dans `FormationCrudController.php` (`@var \App\Entity\User` → `@var User`, déjà importé) ; nouveaux fichiers conformes
- `WorksCrudController` (filtre DQL `ROLE_STAGIAIRE`) : confirmé fonctionnel sans modification

---
> Ce fichier est à placer dans `.claude-tasks/haiku/`, `.claude-tasks/sonnet/` ou `.claude-tasks/opus/` selon le modèle utilisé.
