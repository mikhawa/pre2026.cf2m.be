# 030 - Page works/show : affichage frontend

**Modèle** : Sonnet
**Justification** : Controller + Repository + Template + CSS
**Date** : 2026-03-08

## Fichiers modifiés / créés
- `src/Controller/WorksController.php` (nouveau)
- `src/Repository/WorksRepository.php`
- `templates/works/show.html.twig` (nouveau)
- `templates/formation/show.html.twig`
- `assets/styles/app.css`

## Résumé
- Route `/formation/{formationSlug}/works/{slug}` → `app_works_show`
- `findOnePublishedBySlugAndFormation()` : vérification slug + formation via JOIN
- Template : hero sombre + corps 2 colonnes (description + sidebar auteurs/formation/retour)
- CSS : composants `.cf2m-work-*` cohérents avec la charte graphique
- Liens `href="#"` dans formation/show corrigés vers `app_works_show`

## Résultat
Page `/formation/{slug}/works/{slug}` fonctionnelle avec affichage titre, date, auteurs, description et navigation retour.
