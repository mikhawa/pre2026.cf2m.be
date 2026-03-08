# 039 — Page works/show : affichage frontend d'une réalisation

**Date** : 2026-03-08 12:00
**Branche** : Navigation

## Fichiers créés / modifiés
- `src/Controller/WorksController.php` — **nouveau** : route `app_works_show`
- `src/Repository/WorksRepository.php` — ajout `findOnePublishedBySlugAndFormation()`
- `templates/works/show.html.twig` — **nouveau** : page de présentation d'un work
- `templates/formation/show.html.twig` — correction des `href="#"` → `app_works_show`
- `assets/styles/app.css` — styles `.cf2m-work-*`

## Résumé

### Route
`GET /formation/{formationSlug}/works/{slug}` → `app_works_show`
Retourne 404 si le work n'est pas publié ou si le slug de formation ne correspond pas.

### Design
- **Hero sombre** (fond `--cf2m-dark`) avec titre large, label "Réalisation étudiante", métadonnées (date, formation)
- **Corps** sur fond clair (`--cf2m-light`) : description (card avec header gradient) + sidebar (auteurs avec avatars initialesm lien vers la formation, bouton retour)
- Cohérent avec la charte CF2m (Outfit + DM Sans, cyan, glassmorphisme)

### Repository
`findOnePublishedBySlugAndFormation()` : joint la table `formation` pour valider que le work appartient bien à la bonne formation via son slug.
