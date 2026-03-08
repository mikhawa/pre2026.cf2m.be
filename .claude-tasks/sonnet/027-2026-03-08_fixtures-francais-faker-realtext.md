# 027 - Fixtures en français (Faker realText + tableaux contextuels)

**Modèle** : Sonnet
**Justification** : Modification de plusieurs factories, logique contextuelle
**Date** : 2026-03-08

## Fichiers modifiés
- `src/Factory/FormationFactory.php`
- `src/Factory/WorksFactory.php`
- `src/Factory/PageFactory.php`
- `src/Factory/CommentFactory.php`
- `src/Factory/ContactMessageFactory.php`
- `src/Factory/PartenaireFactory.php`
- `src/Factory/UserFactory.php`

## Résumé
- Remplacement de `faker()->sentence()` / `faker()->paragraph()` / `faker()->paragraphs()` / `faker()->words()` (Lorem Ipsum) par `faker()->realText()` (texte français issu de la locale `fr_BE`)
- Titres, sujets et noms remplacés par des tableaux `const` de données françaises contextuelles au domaine CF2m (centre de formation multimédia)
- `faker()->company()` dans PartenaireFactory remplacé par un tableau de noms de partenaires belges fictifs

## Raison
Le Lorem Ipsum est du latin illisible et non représentatif du contenu final. Les fixtures doivent ressembler à de vraies données françaises pour les démonstrations et les tests.
