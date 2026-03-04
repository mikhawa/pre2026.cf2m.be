# Création entité Formation avec relation ManyToMany User

**Date** : 2026-03-04 09:50
**Modèle** : Haiku

## Fichiers créés/modifiés

### 1. Créé : `src/Entity/Formation.php`
Entité `Formation` avec les champs définis dans le schéma BDD :
- `id` : int unsigned (PK, auto-increment)
- `title` : varchar(255), NotBlank
- `slug` : varchar(255), unique, NotBlank
- `description` : longtext (DBAL Types::TEXT)
- `status` : varchar(20), défaut 'draft', choices
- `createdAt` : DateTimeImmutable, via PrePersist
- `publishedAt` : DateTimeImmutable, nullable

**Relation ManyToMany** :
- Propriété `$responsables` (Collection<int, User>)
- Formation est le côté propriétaire (owning side)
- Attribut : `#[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'formations')]`
- JoinTable : `formation_user`

### 2. Créé : `src/Repository/FormationRepository.php`
Repository Symfony standard extends ServiceEntityRepository<Formation>

### 3. Modifié : `src/Entity/User.php`
Ajout de l'inverse de la relation ManyToMany :
- Propriété `$formations` (Collection<int, Formation>)
- Attribut : `#[ORM\ManyToMany(targetEntity: Formation::class, mappedBy: 'responsables')]`
- Méthodes : getFormations(), addFormation(), removeFormation()
- Initialisation de la collection dans le constructeur

### 4. Modifié : `docs/architecture/database-schema.md`
- Suppression du champ `user_id | int unsigned (FK → User)` de la table Formation
- Ajout de `formation_user` dans la section "Tables de jointure"
- Mise à jour des relations résumées : `Formation >──< User (ManyToMany via formation_user)`

## Raison du changement
La relation ManyToMany permet à une formation d'avoir plusieurs responsables (formateurs), offrant plus de flexibilité que la relation ManyToOne initiale.

## Conventions appliquées
- Déclaration strict_types
- Attributs PHP 8 Doctrine
- Validation avec Assert
- Méthodes setters retournant static
- __toString() retournant le titre
- PHPDoc sur les collections
- Nommage BDD en snake_case
- Tables de jointure en ordre alphabétique
