# 029 - Works publiés sur la page formation

**Modèle** : Sonnet
**Justification** : Repository + Controller + Template
**Date** : 2026-03-08

## Fichiers modifiés
- `src/Repository/WorksRepository.php`
- `src/Controller/FormationController.php`
- `templates/formation/show.html.twig`
- `assets/styles/app.css`

## Résumé
- `findPublishedByFormation()` dans WorksRepository (status=published, orderBy publishedAt DESC)
- FormationController injecte WorksRepository et passe `works` au template
- Template affiche section "Travaux réalisés" avec lien + date si works non vides

## Résultat
Sur `/formation/{slug}`, la liste des works publiés s'affiche sous la description, triée par date de publication décroissante.
