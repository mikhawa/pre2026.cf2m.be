# Analyse architecturale : Permissions fines par Formation pour ROLE_FORMATEUR

**Date d'analyse** : 2026-03-30
**Modèle** : Claude Opus
**Statut** : Analyse préalable — aucun code modifié

---

## Contexte et problème à résoudre

### Hiérarchie des rôles actuelle

```
ROLE_USER
  └── ROLE_STAGIAIRE
        └── ROLE_FORMATEUR
              └── ROLE_ADMIN
                    └── ROLE_SUPER_ADMIN
```

### Comportement actuel du système de révisions

| Rôle | Modification Formation | Modification Works |
|---|---|---|
| ROLE_STAGIAIRE | → PENDING | → PENDING (notifie formateurs) |
| ROLE_FORMATEUR | → PENDING (notifie admins) | → AUTO_APPROVED |
| ROLE_ADMIN | → AUTO_APPROVED | → AUTO_APPROVED |
| ROLE_SUPER_ADMIN | → AUTO_APPROVED | → AUTO_APPROVED |

### Le problème

Le `ROLE_FORMATEUR` est trop grossier. Un formateur désigné comme **responsable** d'une formation spécifique devrait avoir des droits d'administrateur **sur CETTE formation uniquement** (et ses works, et ses stagiaires inscrits), mais rester simple formateur (PENDING) sur les autres formations.

**Exemple concret :**
- Formateur A est responsable de "Développement Web 2026"
- Formateur A modifie "Développement Web 2026" → devrait être AUTO_APPROVED
- Formateur A modifie "Comptabilité 2026" (dont il n'est pas responsable) → doit rester PENDING
- Formateur A peut approuver/rejeter les révisions de works des stagiaires de "Développement Web 2026"

### Périmètre de la demande

Cette évolution impactera à terme :
1. **Formations** : les responsables peuvent auto-approuver leurs formations
2. **Works** : les responsables peuvent approuver les works des stagiaires de leurs formations
3. **Stagiaires** (futur) : accès limité aux ressources de leur formation spécifique

---

## Diagnostic technique de l'existant

Le système actuel repose entièrement sur des **vérifications de rôles statiques** dans les contrôleurs EasyAdmin. Il n'y a **aucun Voter Symfony**, aucune couche d'abstraction de permissions.

### Points de décision critiques

**`FormationCrudController`** :
- `updateEntity()` : `if (!$this->isGranted('ROLE_ADMIN'))` — détermine PENDING vs AUTO_APPROVED
- `approuverHistoriqueFormation()` : `denyAccessUnlessGranted('ROLE_ADMIN')`
- `rejeterHistoriqueFormation()` : `denyAccessUnlessGranted('ROLE_ADMIN')`
- `restaurerHistoriqueFormation()` : `denyAccessUnlessGranted('ROLE_ADMIN')`

**`WorksCrudController`** :
- `updateEntity()` : `if (!$this->isGranted('ROLE_FORMATEUR'))` — seuil formateur/stagiaire
- `approuverHistoriqueWorks()` : `denyAccessUnlessGranted('ROLE_FORMATEUR')`
- `rejeterHistoriqueWorks()` : `denyAccessUnlessGranted('ROLE_FORMATEUR')`
- `restaurerHistoriqueWorks()` : `denyAccessUnlessGranted('ROLE_FORMATEUR')`

**`RevisionService`** :
- `notifyFormateurs()` : notifie TOUS les formateurs (pas seulement les responsables)
- `notifyAdmins()` : notifie tous les admins

### Atout existant

La relation `Formation.responsables` (ManyToMany vers User, table `formation_user`) **existe déjà** et est correctement mappée. C'est le pivot naturel de toute solution — aucune migration BDD n'est nécessaire.

---

## Approche 1 : Voter Symfony avec résolution contextuelle

### Principe

Introduire un (ou plusieurs) Voter(s) Symfony qui résolvent le droit d'un utilisateur en fonction du **contexte de l'entité** (la Formation cible ou la Formation parente d'un Works). Le voter vérifie si l'utilisateur est dans la collection `responsables` de la Formation concernée et accorde un niveau de permission équivalent à ROLE_ADMIN sur cette ressource spécifique.

### Mécanisme technique

1. Créer `src/Security/Voter/FormationVoter.php` implémentant `Symfony\Component\Security\Core\Authorization\Voter\Voter`. Il supporte les attributs :
   - `FORMATION_EDIT_AUTOAPPROVE` — modification auto-approuvée
   - `FORMATION_APPROVE` — approuver une révision en attente
   - `FORMATION_REJECT` — rejeter une révision en attente
   - `FORMATION_RESTORE` — restaurer une version antérieure

2. Créer `src/Security/Voter/WorksVoter.php` avec les attributs équivalents. Ce voter remonte à `$works->getFormation()` pour vérifier si l'utilisateur est responsable de la formation parente.

3. Logique de décision dans chaque voter :
   ```
   ACCESS_GRANTED si :
     - l'utilisateur a ROLE_ADMIN (toujours)
     OU
     - l'utilisateur a ROLE_FORMATEUR ET est dans $formation->getResponsables()
   ACCESS_DENIED sinon
   ```

4. Dans `FormationCrudController::updateEntity()` :
   ```php
   // Avant
   if (!$this->isGranted('ROLE_ADMIN')) { ... }
   // Après
   if (!$this->isGranted('FORMATION_EDIT_AUTOAPPROVE', $entityInstance)) { ... }
   ```

5. Dans les actions approuver/rejeter/restaurer :
   ```php
   // Avant
   $this->denyAccessUnlessGranted('ROLE_ADMIN');
   // Après
   $this->denyAccessUnlessGranted('FORMATION_APPROVE', $formation);
   ```

6. Dans les templates d'historique :
   ```twig
   {# Avant #}
   {% if is_granted('ROLE_ADMIN') %}
   {# Après #}
   {% if is_granted('FORMATION_APPROVE', formation) %}
   ```

7. Adapter `RevisionService::notifyFormateurs()` : ne notifier que les responsables de la formation concernée via `$formation->getResponsables()`, non plus tous les formateurs.

### Avantages

- **Standard Symfony** : pattern officiel recommandé pour les permissions basées sur des objets. Toute l'équipe Symfony connaît ce pattern.
- **Testable en isolation** : un Voter est un service pur, testable unitairement avec un mock de `TokenInterface` et un objet `Formation`. Pas besoin de bootstrap HTTP complet.
- **Centralisé** : la logique "est-il responsable ?" est écrite une seule fois dans le Voter, non dupliquée dans chaque contrôleur.
- **Compatible EasyAdmin nativement** : `isGranted()` et `denyAccessUnlessGranted()` invoquent les voters automatiquement. Aucune adaptation du framework.
- **Twig natif** : `is_granted('FORMATION_APPROVE', formation)` fonctionne directement dans les templates sans extension Twig supplémentaire.
- **Aucune migration BDD** : la relation `formation_user` existe déjà.

### Inconvénients / risques

- **Refactoring des contrôleurs** : 8-10 points de modification dans les deux contrôleurs.
- **`setPermission()` EasyAdmin** : les permissions déclaratives dans `configureActions()` (ex: `setPermission(Action::NEW, 'ROLE_ADMIN')`) ne passent pas d'objet au voter. Il faudra potentiellement utiliser `displayIf()` avec une closure pour les actions d'index.
- **Performances** : le voter doit charger la collection `responsables` pour chaque entité en liste. Mitigeable via fetch EAGER ou QueryBuilder dans le repository.

### Complexité d'implémentation

**Moyenne** — 2 fichiers voters (~80-100 lignes chacun), 8-10 points de modification dans les contrôleurs, adaptation du RevisionService pour les notifications.

### Impact sur les tests existants

**Faible** — les tests unitaires d'entités existants ne sont pas touchés. Nouveaux tests unitaires voters à écrire.

### Extensibilité

Naturellement extensible. Pour limiter les stagiaires à leur formation, il suffit d'ajouter un `InscriptionVoter` qui vérifie via l'entité `Inscription` si le stagiaire est inscrit à la formation parente du Works. Le pattern est identique : un nouveau voter, un nouvel attribut, même mécanisme.

---

## Approche 2 : Service de permission centralisée (PermissionChecker)

### Principe

Créer un service `FormationPermissionService` qui encapsule toute la logique de résolution des droits contextuels. Les contrôleurs appellent ce service au lieu de `isGranted()`. Le service prend en paramètre l'utilisateur, l'action demandée, et l'entité cible.

### Mécanisme technique

1. Créer `src/Service/FormationPermissionService.php` avec des méthodes explicites :
   - `canAutoApproveFormation(User $user, Formation $formation): bool`
   - `canApproveFormationRevision(User $user, Formation $formation): bool`
   - `canAutoApproveWorks(User $user, Works $works): bool`
   - `canApproveWorksRevision(User $user, Works $works): bool`

2. Logique de chaque méthode :
   ```
   retourne true si :
     - l'utilisateur a ROLE_ADMIN
     OU
     - l'utilisateur a ROLE_FORMATEUR ET est dans $formation->getResponsables()
   ```

3. Injecter ce service dans `FormationCrudController` et `WorksCrudController` via le constructeur.

4. Remplacer les appels :
   ```php
   // Avant
   if (!$this->isGranted('ROLE_ADMIN')) { ... }
   // Après
   if (!$this->permissionService->canAutoApproveFormation($user, $formation)) { ... }
   ```

5. Adapter le RevisionService pour utiliser le même service pour les notifications ciblées.

### Avantages

- **Explicite et lisible** : `canAutoApproveFormation` est plus clair que `isGranted('FORMATION_EDIT_AUTOAPPROVE', $entity)`
- **Facile à débugger** : un point d'arrêt dans le service suffit pour tracer toute décision
- **Pas de concept Symfony avancé** : pas besoin de maîtriser le mécanisme des voters, l'ordre d'évaluation, la stratégie de décision

### Inconvénients / risques

- **Hors pattern Symfony** : ne tire pas parti du système d'autorisation natif
- **Non utilisable dans Twig** : impossible d'écrire `{% if is_granted(...) %}` pour les droits contextuels dans les templates. Il faudrait une extension Twig supplémentaire, ou passer les booléens de permission depuis le contrôleur.
- **Deux systèmes en parallèle** : rôles Symfony pour l'access control global + service pour les permissions fines → risque de confusion sur "où vérifier quoi"
- **Scalabilité** : chaque nouveau contexte (pages, inscriptions, commentaires par formation) gonfle le service — risque de "god object" de permissions

### Complexité d'implémentation

**Faible à moyenne** — un seul fichier service à créer, les modifications dans les contrôleurs sont mécaniques.

### Impact sur les tests existants

**Nul**

### Extensibilité

Extensible, mais avec croissance linéaire du service. À terme, probable migration vers les voters pour éviter la duplication.

---

## Approche 3 : Middleware EasyAdmin via EventSubscriber

### Principe

Intercepter les événements EasyAdmin (`BeforeEntityUpdatedEvent`, etc.) dans un EventSubscriber dédié qui injecte la logique de permission contextuelle. Les contrôleurs ne sont pas (ou très peu) modifiés : c'est le subscriber qui décide dynamiquement si une action est auto-approuvée ou en attente, en fonction de la relation `responsables`.

### Mécanisme technique

1. Créer `src/EventSubscriber/FormationPermissionSubscriber.php` écoutant :
   - `BeforeEntityUpdatedEvent` : vérifier si l'utilisateur est responsable. Si oui, marquer la révision comme auto-approved via un service transient.
   - Les événements CRUD EasyAdmin pour les actions custom (approuver/rejeter).

2. Introduire un service `RevisionContext` communicant le niveau de privilège contextuel au contrôleur.

3. Dans les contrôleurs, lire le flag :
   ```php
   if (!$this->revisionContext->isAutoApproveAllowed()) { ... }
   ```

4. Le subscriber pose ce flag avant que `updateEntity` ne s'exécute, en analysant la Formation et l'utilisateur courant.

### Avantages

- **Séparation des préoccupations** : la logique de permission est extraite des contrôleurs
- **En théorie peu de modifications dans les contrôleurs**
- **Centralisé pour EasyAdmin** : un seul subscriber gère Formation et Works

### Inconvénients / risques

- **Couplage implicite** : la décision est "invisible" dans le flux d'exécution. Un développeur lisant `FormationCrudController` ne voit pas que la permission a été résolue en amont par un subscriber.
- **Fragilité des événements EasyAdmin** : les routes `#[AdminRoute]` custom (approuver/rejeter) ne déclenchent pas forcément les événements CRUD standard. Il faudrait écouter des événements kernel (`kernel.controller`), augmentant la complexité.
- **État transient mutable partagé** : communiquer le résultat du subscriber au contrôleur via un service context introduit un couplage risqué. Bugs subtils possibles.
- **Tests lourds** : tester un subscriber EasyAdmin nécessite un contexte HTTP complet.
- **Fragilité future** : si EasyAdmin change son système d'événements, le subscriber peut casser silencieusement.

### Complexité d'implémentation

**Élevée** — gestion des événements EasyAdmin pour les actions custom non triviale, communication subscriber ↔ contrôleur ajoutant de la complexité architecturale.

### Impact sur les tests existants

**Faible** sur les tests unitaires. Tests fonctionnels futurs plus complexes.

### Extensibilité

Risquée : la logique pour stagiaires par formation interagirait avec le `createIndexQueryBuilder` déjà présent dans le contrôleur, créant un risque de conflit entre subscriber et contrôleur.

---

## Tableau comparatif

| Critère | Approche 1 (Voter) | Approche 2 (Service) | Approche 3 (Subscriber) |
|---|---|---|---|
| Standard Symfony | ✅ Pattern officiel | ⚠️ Custom | ❌ Contournement |
| Utilisable dans Twig | ✅ Natif | ❌ Extension nécessaire | ❌ Impossible |
| Testabilité | ✅ Unitaire simple | ✅ Unitaire simple | ⚠️ Fonctionnel lourd |
| Lisibilité du code | ✅ Bonne | ✅ Très bonne | ❌ Couplage implicite |
| Migration BDD | ✅ Aucune | ✅ Aucune | ✅ Aucune |
| Complexité | Moyenne | Faible-Moyenne | Élevée |
| Extensibilité | ✅ Excellente | ⚠️ Croissance linéaire | ❌ Risquée |
| Impact tests existants | Faible | Nul | Faible |

---

## Recommandation : Approche 1 — Voter Symfony

Recommandation sans ambiguïté pour les raisons suivantes :

1. **Pattern canonique Symfony** — le problème posé ("droits différents selon l'objet concerné") est exactement le cas d'usage pour lequel les Voters ont été conçus. La documentation officielle recommande explicitement ce pattern.

2. **Intégration native EasyAdmin et Twig** — zéro adaptation du framework, `isGranted()` et `is_granted()` invoquent les voters automatiquement.

3. **La relation existe déjà** — `Formation.responsables` et `formation_user` sont en place. Aucune migration.

4. **Chaînage Works → Formation naturel** — `$works->getFormation()->getResponsables()` est trivial grâce au mapping Doctrine existant.

5. **Extensibilité prouvée** — chaque nouveau contexte (stagiaire/inscription, page par formation) = un voter supplémentaire, petit, testé unitairement, isolé.

---

## Plan de séquençage recommandé (implémentation future)

### Phase 1 — Formations
1. Créer `src/Security/Voter/FormationVoter.php` avec les attributs `FORMATION_EDIT_AUTOAPPROVE`, `FORMATION_APPROVE`, `FORMATION_REJECT`, `FORMATION_RESTORE`
2. Adapter `FormationCrudController` (4-5 remplacements d'appels `isGranted`)
3. Adapter `templates/admin/formation/historique.html.twig`

### Phase 2 — Works
4. Créer `src/Security/Voter/WorksVoter.php` (remonte à `$works->getFormation()`)
5. Adapter `WorksCrudController` (même logique)
6. Adapter `templates/admin/works/historique.html.twig`

### Phase 3 — Notifications
7. Adapter `RevisionService::notifyFormateurs()` pour cibler uniquement les responsables de la formation concernée

### Phase 4 — Tests et documentation
8. Écrire les tests unitaires des voters
9. Mettre à jour `HIERARCHIE.md`

### Phase 5 — Stagiaires par formation (futur)
10. `InscriptionVoter` : vérifie si le stagiaire est inscrit à la formation parente du Works
11. Limiter les vues index à leurs formations/works dans les contrôleurs

---

## Fichiers clés pour l'implémentation

- `src/Controller/Admin/FormationCrudController.php` — 4-5 points de modification
- `src/Controller/Admin/WorksCrudController.php` — 4-5 points de modification
- `src/Service/RevisionService.php` — adapter `notifyFormateurs()`
- `src/Entity/Formation.php` — relation `responsables` déjà présente
- `src/Entity/Works.php` — relation `formation` déjà présente
- `templates/admin/formation/historique.html.twig` — condition `is_granted`
- `templates/admin/works/historique.html.twig` — condition `is_granted`
- `config/packages/security.yaml` — vérifier la stratégie de vote (affirmative par défaut)
