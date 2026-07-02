# Proposition — Gestion des stagiaires par formation

**Date** : 2026-07-02
**Statut** : Proposition — non implémenté
**Auteur** : à valider

> Ce document est une **proposition d'architecture**. Aucun code n'est modifié.
> Il complète (sans les dupliquer) :
> - [`../../HIERARCHIE.md`](../../HIERARCHIE.md) — état actuel des droits
> - [`permissions-fines-formations.md`](permissions-fines-formations.md) — pattern « responsable de formation » déjà en production
> - [`database-schema.md`](database-schema.md) — schéma BDD existant

---

## 1. Problème actuel

Aujourd'hui, `ROLE_STAGIAIRE` est un rôle **global** :

- Il est stocké manuellement dans la colonne `roles` (JSON) de l'entité `User`.
- Il est attribué/retiré via une checkbox dans `UserCrudController` (EasyAdmin).
- Il n'est **rattaché à aucune formation** : un stagiaire est « stagiaire du CF2m », pas « stagiaire de la formation X ».

Le besoin exprimé : qu'un `ROLE_FORMATEUR`, `ROLE_ADMIN` ou `ROLE_PEDAGO` puisse **inscrire (ou retirer) un `ROLE_USER` dans une formation précise**, ce qui le ferait **devenir stagiaire de cette formation**. Une formation devient alors une « classe » dont on sélectionne les membres.

### Différence de modèle avec le pattern « responsable » (important)

Le pattern existant (`responsables` d'une Formation) et le besoin « stagiaires » ne sont **pas le même mécanisme**, même si le Voter aura une structure similaire :

| | Rôle **scopé** par l'appartenance (pattern `responsables`) | Rôle **obtenu** par l'appartenance (besoin `stagiaires`) |
|---|---|---|
| Rôle global | `ROLE_FORMATEUR` est déjà attribué manuellement | `ROLE_USER` sans rôle particulier au départ |
| Effet de l'appartenance | Ajoute des **droits contextuels** sur cette formation | **Fait acquérir** le rôle `ROLE_STAGIAIRE` |
| Retrait de l'appartenance | L'utilisateur reste `ROLE_FORMATEUR` | L'utilisateur peut **perdre** `ROLE_STAGIAIRE` (voir §4.3) |

Autrement dit : un responsable *a déjà* son rôle et l'appartenance ne fait que le préciser ; un stagiaire *gagne* son rôle **grâce à** l'appartenance. Cette asymétrie est le point central des choix ci-dessous.

### Contrainte technique à ne pas casser

`WorksCrudController` (≈ ligne 188) filtre les stagiaires par une requête DQL directe sur la colonne :

```php
->andWhere('entity.roles LIKE :stagiaire')
->setParameter('stagiaire', '%ROLE_STAGIAIRE%')
```

Toute solution qui rendrait `ROLE_STAGIAIRE` **purement calculé** (jamais écrit en base) **casse cette requête** et d'autres usages (filtre liste `UserCrudController`, templates, fixtures). Deux stratégies possibles, retenues différemment selon l'option :

- **A. Synchronisation physique** : des helpers écrivent/retirent automatiquement `ROLE_STAGIAIRE` dans la colonne `roles` quand l'appartenance change. Le code existant continue de fonctionner sans réécriture.
- **B. Rôle calculé** : `getRoles()` ajoute `ROLE_STAGIAIRE` à la volée si l'utilisateur a ≥ 1 appartenance. Plus « propre » conceptuellement, mais impose de **réécrire** la requête DQL et les filtres.

> **Recommandation transverse** : privilégier la **stratégie A** (synchronisation physique). Elle préserve la rétrocompatibilité et évite de toucher au code de filtrage existant. Le rôle reste la *source de vérité* pour la sécurité globale, l'appartenance en devient le *déclencheur*.

---

## 2. Vue d'ensemble des options

| | Option 1 — ManyToMany direct | Option 2 — Entité pivot légère | Option 3 — Entité `Classe`/`Groupe` |
|---|---|---|---|
| Nouvelle table | `formation_stagiaire` (pivot auto) | `formation_stagiaire` (entité) | `classe` + `classe_user` |
| Traçabilité (qui/quand) | ❌ | ✅ (`addedBy`, `addedAt`) | ✅ (sur l'entité Classe/pivot) |
| Plusieurs groupes / formation | ❌ | ❌ | ✅ |
| Complexité | Faible | Moyenne | Élevée |
| Proximité avec le besoin littéral « une formation = une classe » | Bonne | Bonne | Meilleure (mais sur-dimensionnée) |

Toutes les options partagent la **même logique de droits** (§3) et la **même stratégie de synchronisation A** (§1).

---

## 3. Droits communs à toutes les options

### 3.1 `role_hierarchy` (security.yaml)

**Aucun changement requis.** `ROLE_STAGIAIRE` reste sous `ROLE_USER`, et l'`access_control` `^/admin → ROLE_STAGIAIRE` continue de protéger la zone admin. C'est justement pourquoi la **stratégie A** (garder `ROLE_STAGIAIRE` en base) est préférable : la hiérarchie et les contrôles d'accès globaux restent inchangés.

### 3.2 Nouveau Voter — `FormationStagiaireVoter`

Nouvel attribut : **`FORMATION_MANAGE_STAGIAIRES`** (sujet : `Formation`). Structure calquée sur `FormationVoter`, mais avec une nuance : ici on autorise `ROLE_FORMATEUR` **uniquement s'il est responsable de la formation**, exactement comme pour l'édition.

```php
// Esquisse illustrative — FormationStagiaireVoter
protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
{
    $user = $token->getUser();
    $formation = $subject; // Formation

    if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_PEDAGO')) {
        return true; // toute formation
    }
    if (!$this->security->isGranted('ROLE_FORMATEUR')) {
        return false;
    }
    return $formation->getResponsables()->contains($user); // sa formation uniquement
}
```

### 3.3 Qui peut ajouter/retirer qui — récapitulatif

| Acteur | Portée | Peut gérer les stagiaires ? |
|---|---|---|
| `ROLE_FORMATEUR` non responsable | — | ❌ |
| `ROLE_FORMATEUR` responsable | Cette formation | ✅ |
| `ROLE_PEDAGO` | Toute formation | ✅ |
| `ROLE_ADMIN` / `ROLE_SUPER_ADMIN` | Toute formation | ✅ |
| Cible ajoutée/retirée | — | N'importe quel `ROLE_USER` (voir §4.5 sur les cibles éligibles) |

Cette logique est **cohérente avec le pattern existant** (cf. `permissions-fines-formations.md`) : admin/pédago partout, formateur seulement sur son périmètre.

---

## 4. Détail des options

### Option 1 — ManyToMany direct `Formation ↔ User`

**Le plus simple. Calqué sur `responsables`, déjà en prod.**

#### Schéma

- `Formation.stagiaires` : `Collection<User>`, ManyToMany, table pivot **`formation_stagiaire`**.
- `User.formationsStagiaire` : côté inverse (mappedBy), pour lister les formations d'un stagiaire.

```php
// Esquisse — Formation
#[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'formationsStagiaire')]
#[ORM\JoinTable(name: 'formation_stagiaire')]
private Collection $stagiaires;
```

#### Synchronisation `ROLE_STAGIAIRE` (stratégie A)

Les méthodes `addStagiaire()` / `removeStagiaire()` déclenchent, via un **service** (`StagiaireService`) ou un **listener Doctrine**, la mise à jour du rôle global :

```php
// Esquisse — StagiaireService::synchroniserRole()
if ($user->getFormationsStagiaire()->isEmpty()) {
    $user->removeRole('ROLE_STAGIAIRE'); // plus aucune formation → perd le rôle
} else {
    $user->addRole('ROLE_STAGIAIRE');    // au moins une formation → garde le rôle
}
```

> **Ne pas mettre cette logique dans l'entité elle-même** (une entité ne doit pas décider de la persistance) : passer par un service appelé depuis le controller EasyAdmin, ou un `postUpdate`/`postPersist` listener sur la table pivot.

#### UI EasyAdmin

Ajouter un champ `AssociationField('stagiaires')` (multi-sélection Ajax) dans `FormationCrudController`, visible/éditable seulement si `is_granted('FORMATION_MANAGE_STAGIAIRES', formation)`. La liste des candidats peut être filtrée sur les `ROLE_USER` actifs.

#### Rétrocompatibilité / migration

Les utilisateurs actuels ayant `ROLE_STAGIAIRE` en base **conservent leur rôle** (colonne inchangée). Ils ne sont simplement rattachés à aucune formation tant qu'un gestionnaire ne les ajoute pas. Aucun backfill obligatoire ; possibilité (optionnelle) d'un script de rattachement manuel a posteriori.

#### Avantages / inconvénients

| ✅ Avantages | ❌ Inconvénients |
|---|---|
| Migration minimale (une table pivot) | Aucune traçabilité (qui a ajouté qui, quand) |
| Réutilise un pattern éprouvé (`responsables`) | Pas de métadonnées par inscription (date d'entrée, statut) |
| Aucun changement de `role_hierarchy` | « Grand contrôle » limité |

---

### Option 2 — Entité pivot légère avec métadonnées (recommandée)

**Même relation que l'option 1, mais la table pivot devient une entité** portant `addedBy` et `addedAt`. Répond directement au souhait « garder un grand contrôle ».

#### Schéma

Nouvelle entité **`FormationStagiaire`** (ou `Appartenance`) :

| Champ | Type | Rôle |
|---|---|---|
| `formation` | ManyToOne `Formation` | la formation-classe |
| `user` | ManyToOne `User` | le stagiaire |
| `addedBy` | ManyToOne `User` (nullable) | qui a inscrit ce stagiaire |
| `addedAt` | `datetime_immutable` | quand |
| (optionnel) `active` | `bool` | permet un retrait « soft » sans perdre l'historique |

Contrainte d'unicité `(formation, user)`. `Formation` a une `OneToMany` vers `FormationStagiaire`.

#### Synchronisation `ROLE_STAGIAIRE`

Identique à l'option 1 (stratégie A), déclenchée à la création/suppression d'une ligne `FormationStagiaire`. Si le champ optionnel `active` est utilisé, la synchro compte les appartenances **actives** uniquement.

#### UI EasyAdmin

Deux approches :
1. **`CollectionField`** sur `FormationStagiaire` dans `FormationCrudController` (formulaire imbriqué, `addedBy`/`addedAt` en lecture seule, remplis automatiquement).
2. Ou un **CRUD dédié** `FormationStagiaireCrudController` filtré par voter — plus verbeux mais plus contrôlable.

`addedBy` = utilisateur courant, `addedAt` = maintenant, remplis côté serveur (jamais depuis le formulaire).

#### Rétrocompatibilité / migration

Comme l'option 1 : les `ROLE_STAGIAIRE` existants gardent leur rôle. Différence : on peut, lors d'un backfill optionnel, créer des lignes `FormationStagiaire` avec `addedBy = null` et `addedAt = date de migration` pour matérialiser l'existant sans perdre la cohérence.

#### Avantages / inconvénients

| ✅ Avantages | ❌ Inconvénients |
|---|---|
| **Traçabilité complète** (qui/quand) → « grand contrôle » | Une entité de plus à maintenir |
| Extensible (statut, date de sortie, motif…) sans refonte | UI un peu plus lourde (formulaire imbriqué) |
| Reste **une seule** relation logique formation↔user | Légèrement plus de code que l'option 1 |
| Aucun changement de `role_hierarchy` | |

---

### Option 3 — Entité `Classe`/`Groupe` intermédiaire

**La plus proche de la formulation littérale « une formation deviendrait une classe (ou plusieurs) », mais probablement une sur-ingénierie au vu du besoin actuel.**

#### Schéma

Entité **`Classe`** entre `Formation` et `User` :

```
Formation 1 ──── n Classe n ──── n User (stagiaires, via classe_user)
```

| Entité | Champs clés |
|---|---|
| `Classe` | `formation` (ManyToOne), `nom`, `anneeScolaire`, `dateDebut`/`dateFin` |
| `classe_user` | pivot Classe↔User (éventuellement enrichi comme l'option 2) |

Une formation peut avoir **plusieurs classes** (promotions, sessions, groupes du matin/soir…). Les `Works` pourraient à terme être rattachés à une `Classe` plutôt qu'à la `Formation` directement.

#### Synchronisation `ROLE_STAGIAIRE`

Stratégie A : un utilisateur est `ROLE_STAGIAIRE` s'il appartient à ≥ 1 classe. Le Voter remonte `Classe → Formation → responsables` (comme `WorksVoter` remonte `Works → Formation`).

#### UI EasyAdmin

Nouveau CRUD `ClasseCrudController` + gestion des membres par classe. Plus de surface d'écran, plus de navigation (formation → classes → membres).

#### Rétrocompatibilité / migration

Plus coûteuse : il faut créer une « classe par défaut » par formation existante pour y rattacher les stagiaires actuels, adapter `Works` si on veut lier au niveau classe, et migrer les usages `ROLE_STAGIAIRE`.

#### Avantages / inconvénients

| ✅ Avantages | ❌ Inconvénients |
|---|---|
| Plusieurs groupes/promotions par formation | **Complexité nettement supérieure** |
| Modélisation « scolaire » réaliste à long terme | Migration lourde, impacte potentiellement `Works` |
| Traçabilité fine possible | Va à l'encontre de « sans trop complexifier » |
| | Besoin actuel = 1 groupe / formation → YAGNI |

---

## 5. Points transverses à traiter

### 5.1 Synchronisation du flag physique
Traitée par la **stratégie A** dans les trois options (§1). Le rôle reste écrit en base ; l'appartenance ne fait que déclencher son ajout/retrait via un service ou listener. Aucun code de filtrage existant (DQL Works, filtres User, templates) n'a besoin d'être réécrit.

### 5.2 Différence rôle obtenu vs rôle scopé
Le Voter (§3.2) a la **même forme** que `FormationVoter`, mais il régit **qui gère** les stagiaires — pas les droits du stagiaire lui-même. Le droit du stagiaire (`ROLE_STAGIAIRE`) vient, lui, de la **synchronisation** (§5.1), pas du Voter. Bien séparer ces deux mécanismes évite la confusion avec le pattern « responsable ».

### 5.3 Retrait de la dernière formation
**Conséquence logique et assumée** : si on retire un utilisateur de sa **dernière** formation, il perd `ROLE_STAGIAIRE` globalement → il n'a plus accès à `/admin` (l'`access_control` exige `ROLE_STAGIAIRE`). C'est le comportement attendu : un ex-stagiaire sans formation n'a plus à accéder à la zone stagiaire. À signaler dans l'UI (message de confirmation) pour éviter les retraits accidentels. L'option 2 avec `active=false` permet, si souhaité, de conserver l'historique tout en retirant le rôle.

### 5.4 Lien optionnel avec les `Inscription` (préinscription publique)
**Piste, non obligatoire.** Aujourd'hui l'approbation d'une `Inscription` (`treat`/`treatBy`) et l'ajout d'un stagiaire sont **déconnectés**. On pourrait, au moment où une inscription est marquée « traitée/acceptée » :
1. créer (ou retrouver) le compte `User` correspondant,
2. l'ajouter comme stagiaire de la `Formation` de l'inscription (avec `addedBy` = l'admin qui traite, en option 2).

À documenter comme **évolution future** : cela touche au workflow d'inscription et à la création de comptes (mails de bienvenue), et mérite sa propre proposition.

### 5.5 Emails / notifications
Aucune obligation. Piste : envoyer un mail « vous avez été inscrit à la formation X » lors de l'ajout (réutiliser l'infra `RevisionService`/mailer existante). À traiter séparément — voir `HIERARCHIE.md` §« Mails ».

---

## 6. Recommandation

Le compromis demandé est **« grand contrôle sans trop complexifier »**.

- **Option 1** est la plus simple mais sacrifie la traçabilité → contrôle insuffisant.
- **Option 3** offre le plus de contrôle mais complexifie fortement (migration, `Works`, UI) pour un besoin encore mono-groupe → sur-ingénierie aujourd'hui.
- **Option 2** est le point d'équilibre : **une seule relation logique** (donc simplicité proche de l'option 1), mais enrichie de `addedBy`/`addedAt` qui apportent exactement le « grand contrôle » recherché.

> ### ✅ Recommandation : **Option 2 (entité pivot légère)** avec **stratégie A** (synchronisation physique de `ROLE_STAGIAIRE`) et **nouveau `FormationStagiaireVoter`** (`FORMATION_MANAGE_STAGIAIRES`).

**À faire maintenant :**
1. Entité `FormationStagiaire` (`formation`, `user`, `addedBy`, `addedAt`, unicité).
2. Service de synchronisation `ROLE_STAGIAIRE` (ajout/retrait automatique).
3. `FormationStagiaireVoter` + intégration UI dans `FormationCrudController`.
4. Message de confirmation au retrait (cf. §5.3).

**À garder pour plus tard (évolutions futures) :**
- L'entité `Classe`/`Groupe` (option 3), **seulement si** le besoin de plusieurs promotions par formation apparaît réellement. L'option 2 pourra être migrée vers l'option 3 sans perte de données (les lignes `FormationStagiaire` deviennent des `classe_user`).
- Le lien automatique `Inscription → stagiaire` (§5.4) et les notifications (§5.5).

Cette trajectoire donne un contrôle fort **immédiatement** tout en gardant l'architecture légère et **une porte de sortie propre** vers l'option 3 si les besoins grandissent.
