# 038 — Works publiés sur la page formation (tri publishedAt DESC)

**Date** : 2026-03-08 11:30
**Branche** : Navigation

## Fichiers modifiés
- `src/Repository/WorksRepository.php` — ajout de `findPublishedByFormation()`
- `src/Controller/FormationController.php` — injection `WorksRepository`, passage de `works` au template
- `templates/formation/show.html.twig` — section "Travaux réalisés"
- `assets/styles/app.css` — style `.cf2m-works-link`

## Résumé des changements

### WorksRepository
Nouvelle méthode `findPublishedByFormation(int $formationId)` : retourne les works avec `status = 'published'` pour la formation donnée, triés par `publishedAt DESC`.

### FormationController
Injection de `WorksRepository`, appel de `findPublishedByFormation()`, variable `works` passée au template.

### Template
Section "Travaux réalisés" affichée uniquement si des works existent, avec lien et date de publication pour chaque work.

## Raison
Permettre aux visiteurs de consulter les réalisations des étudiants directement depuis la page d'une formation.
