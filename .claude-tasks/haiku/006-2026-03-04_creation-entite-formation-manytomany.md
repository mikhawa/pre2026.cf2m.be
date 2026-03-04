# Tâche 006 : Création entité Formation ManyToMany User

**Modèle** : Haiku
**Date** : 2026-03-04

## Justification
Création de l'entité `Formation` avec ses champs de base et mise en place de la relation ManyToMany vers `User` (responsables). Cette relation remplace la relation ManyToOne initialement prévue, permettant à une formation d'avoir plusieurs responsables.

## Fichiers modifiés/créés
1. **Créé** : `src/Entity/Formation.php`
   - Entité complète avec attributs Doctrine
   - Champs : id, title, slug, description, status, createdAt, publishedAt
   - Relation ManyToMany vers User (responsables)
   - Méthodes : getters, setters, addResponsable(), removeResponsable()
   - PrePersist pour createdAt
   - __toString() retournant le titre

2. **Créé** : `src/Repository/FormationRepository.php`
   - Repository Symfony standard
   - Extends ServiceEntityRepository

3. **Modifié** : `src/Entity/User.php`
   - Ajout de la collection `$formations` (Collection<int, Formation>)
   - Ajout des méthodes : getFormations(), addFormation(), removeFormation()
   - Initialisation dans le constructeur

4. **Modifié** : `docs/architecture/database-schema.md`
   - Suppression du champ `user_id` de Formation
   - Ajout de `formation_user` dans les tables de jointure
   - Mise à jour des relations résumées : `Formation >──< User (ManyToMany via formation_user)`

## Conventions appliquées
- `declare(strict_types=1)` obligatoire
- Attributs PHP 8 Doctrine (#[ORM\*])
- #[ORM\PrePersist] pour createdAt
- #[Assert\*] pour la validation
- Setters retournent `static`
- `__toString()` retourne title
- id unsigned
- PHPDoc `/** @var Collection<int, Entity> */` sur les collections
- Table de jointure : `formation_user` (ordre alphabétique)

## Résumé
Entité Formation créée avec relation ManyToMany bidirectionnelle vers User.
