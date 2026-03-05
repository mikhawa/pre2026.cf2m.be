# 013 — Page d'accueil responsive

**Modèle** : Sonnet
**Justification** : Controller + templates Twig + CSS + repositories — catégorie Sonnet
**Date** : 2026-03-05

## Fichiers modifiés

- `importmap.php` — bootstrap, @popperjs/core, bootstrap/dist/css/bootstrap.min.css ajoutés
- `assets/app.js` — imports Bootstrap JS + CSS
- `assets/styles/app.css` — variables CSS, navbar, hero, cards, partenaires, footer
- `templates/base.html.twig` — structure HTML5 complète (lang, viewport, navbar sticky, footer)
- `templates/home/index.html.twig` — 3 sections : hero, formations, partenaires
- `src/Controller/HomeController.php` — injection FormationRepository + PartenaireRepository
- `src/Repository/FormationRepository.php` — `findAllPublished()`
- `src/Repository/PartenaireRepository.php` — `findAllActive()`

## Résumé

Page d'accueil responsive Bootstrap 5 avec :
- **Navbar sticky** : CF2m (texte), liens Accueil/Formations/À propos/Contact, bouton Se connecter, gestion auth Twig
- **Hero** : gradient bleu marine → bleu moyen, titre H1, sous-titre, CTA orange → ancre #formations
- **Grille formations** : `row-cols-1 row-cols-md-2 row-cols-lg-3`, cards avec header navy, description tronquée 150 chars, date publication
- **Section partenaires** : fond clair, flex-wrap de pastilles cliquables (lien si URL disponible)
- **Footer** : navy + bordure accent, copyright dynamique

## Palette couleurs CF2m

| Variable | Valeur | Usage |
|----------|--------|-------|
| `--cf2m-navy` | `#1a2e4a` | Navbar, hero, footer, card header |
| `--cf2m-navy-md` | `#2d5282` | Fin du gradient hero |
| `--cf2m-accent` | `#e85d04` | CTA, badges, soulignement titres |
| `--cf2m-light` | `#f1f5f9` | Fond section partenaires |

## Résultat

- `bin/console lint:twig` → OK (2 fichiers valides)
- `bin/console cache:clear` → OK
- Tests : 88 tests, 279 assertions — 100% vert
