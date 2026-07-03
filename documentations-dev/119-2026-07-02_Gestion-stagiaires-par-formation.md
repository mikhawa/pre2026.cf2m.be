# 119 — Gestion des stagiaires par formation (entité pivot FormationStagiaire)

**Date :** 2026-07-02
**Commit :** `201bb57` (implémentation) · `d1e64f5` (note d'architecture)
**Branche :** `feature/25-hierarchie-and-classes`

---

## Contexte

Jusqu'ici, le rôle `ROLE_STAGIAIRE` était un simple rôle global attribué à un utilisateur, sans lien avec une formation précise. Il n'existait aucun moyen, depuis l'admin, de savoir quel stagiaire suit quelle formation, ni de laisser un formateur responsable gérer lui-même la liste de ses stagiaires.

Une note de conception préalable — `docs/architecture/proposition-gestion-stagiaires-formation.md` — a comparé plusieurs options et retenu l'**Option 2** : une entité pivot légère plutôt qu'une relation ManyToMany nue, afin de tracer qui a ajouté quel stagiaire et quand.

---

## Fonctionnalité ajoutée

### Entité pivot `FormationStagiaire`

Nouvelle entité reliant `Formation` et `User`, avec :
- contrainte d'unicité `(formation, user)` — un stagiaire ne peut être rattaché deux fois à la même formation ;
- traçabilité `addedBy` (le gestionnaire ayant fait le rattachement) et `addedAt` ;
- clés étrangères `ON DELETE CASCADE` sur `formation_id` / `user_id`, `ON DELETE SET NULL` sur `added_by_id` (la ligne est conservée si le gestionnaire est supprimé).

`Formation` reçoit une relation `OneToMany` vers `FormationStagiaire` (`src/Entity/Formation.php`).

### Synchronisation physique de `ROLE_STAGIAIRE`

Stratégie retenue : le rôle global `ROLE_STAGIAIRE` reste écrit en base sur `User`, mais il est désormais **synchronisé automatiquement** avec la présence d'au moins un rattachement `FormationStagiaire` :
- ajout d'un premier rattachement → `ROLE_STAGIAIRE` ajouté à l'utilisateur ;
- retrait du dernier rattachement → `ROLE_STAGIAIRE` retiré (avec message flash explicite prévenant la perte d'accès à `/admin`).

`User` gagne deux helpers `addRole()` / `removeRole()` qui manipulent directement le tableau `$this->roles` (les méthodes `getRoles()`/`setRoles()` existantes ne sont pas modifiées, pour ne rien casser côté Security).

Le filtre DQL existant de `WorksCrudController` (`entity.roles LIKE '%ROLE_STAGIAIRE%'`) continue de fonctionner sans modification, la synchro étant purement physique sur la colonne `roles`.

### Service `StagiaireService`

- `ajouterStagiaire()` : idempotent — si l'utilisateur est déjà stagiaire de la formation, retourne le rattachement existant au lieu d'en créer un doublon (la contrainte d'unicité en base sert de garde-fou).
- `retirerStagiaire()` : retourne `true` si c'était la dernière formation du stagiaire (pour déclencher le retrait de `ROLE_STAGIAIRE` et le message flash côté contrôleur).

### Voter `FormationStagiaireVoter`

Nouveau voter contextuel, attribut `FORMATION_MANAGE_STAGIAIRES`, toujours évalué avec un sujet `Formation` (pas de cas `CREATE` sans sujet). Autorise :
- `ROLE_ADMIN` / `ROLE_PEDAGO` sur n'importe quelle formation ;
- `ROLE_FORMATEUR` uniquement s'il est responsable de la formation concernée.

Tests unitaires dédiés dans `tests/Security/Voter/FormationStagiaireVoterTest.php`.

### UI EasyAdmin

Nouvelle action « Stagiaires » dans `FormationCrudController` (pattern similaire à `historiqueFormation`), avec un template dédié `templates/admin/formation/stagiaires.html.twig` :
- liste des stagiaires rattachés à la formation ;
- formulaire d'ajout via `<select>` (les candidats proposés sont les utilisateurs non encore stagiaires de cette formation) ;
- retrait avec confirmation JS.

L'autocomplete Ajax sur le `<select>` est notée comme amélioration future, non implémentée à ce stade.

---

## Hors scope

Reporté (cf. proposition §5.4/§5.5) :
- lien automatique entre une `Inscription` acceptée et la création du rattachement stagiaire ;
- notifications e-mail lors de l'ajout/retrait.

---

## Fichiers modifiés / créés

- `src/Entity/FormationStagiaire.php` (créé)
- `src/Repository/FormationStagiaireRepository.php` (créé)
- `src/Service/StagiaireService.php` (créé)
- `src/Security/Voter/FormationStagiaireVoter.php` (créé)
- `tests/Security/Voter/FormationStagiaireVoterTest.php` (créé)
- `templates/admin/formation/stagiaires.html.twig` (créé)
- `migrations/Version20260702093000.php` (créé, migration écrite à la main, sans backfill des `ROLE_STAGIAIRE` déjà existants)
- `src/Entity/Formation.php` (relation `OneToMany` vers `FormationStagiaire`)
- `src/Entity/User.php` (helpers `addRole()` / `removeRole()`)
- `src/Controller/Admin/FormationCrudController.php` (nouvelle action + méthodes de gestion des stagiaires)
- `docs/architecture/proposition-gestion-stagiaires-formation.md` (note de conception, options envisagées)

---

## Vérifications effectuées

- `doctrine:schema:validate --skip-sync` → mapping correct
- `doctrine:migrations:migrate` → migration `Version20260702093000` appliquée
- `lint:container` → OK (autowiring `StagiaireService`, `FormationStagiaireRepository`)
- `bin/phpunit tests/Security/Voter/` → 81 tests, 87 assertions, aucune régression
- Filtre DQL `ROLE_STAGIAIRE` de `WorksCrudController` : confirmé fonctionnel sans modification

---

## Traçabilité

Implémenté par le modèle **Opus** (choix justifié par la conception de sécurité : voter contextuel + stratégie de synchronisation de rôle impactant l'accès `/admin`). Détails complets : `.claude-tasks/opus/185-2026-07-02_gestion-stagiaires-par-formation.md`.