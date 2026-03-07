# 023 — Page d'accueil responsive Bootstrap 5

**Date** : 2026-03-05 10:00
**Auteur** : Claude Sonnet (tâche 013)

## Fichiers modifiés

| Fichier | Action |
|---------|--------|
| `importmap.php` | Bootstrap 5.x, @popperjs/core, bootstrap CSS ajoutés via `importmap:require` |
| `assets/app.js` | `import 'bootstrap'` + `import 'bootstrap/dist/css/bootstrap.min.css'` |
| `assets/styles/app.css` | Palette CSS custom (variables + styles navbar/hero/cards/partenaires/footer) |
| `templates/base.html.twig` | Structure HTML5 : lang="fr", viewport, navbar sticky-top, footer |
| `templates/home/index.html.twig` | 3 sections : Hero, Formations publiées, Partenaires actifs |
| `src/Controller/HomeController.php` | Injection des 2 repositories, données passées au template |
| `src/Repository/FormationRepository.php` | `findAllPublished()` — DQL avec filtre status='published' |
| `src/Repository/PartenaireRepository.php` | `findAllActive()` — DQL avec filtre isActive=true |

## Résumé

Création d'une page d'accueil responsive complète avec charte graphique CF2m (navy #1a2e4a + accent orange #e85d04). La navbar sticky affiche les liens principaux et gère l'état d'authentification (bouton Connexion si non connecté, lien Profil + Déconnexion si connecté). Les données (formations publiées, partenaires actifs) sont chargées dynamiquement via les repositories.

## Raison

Le site avait besoin d'une page d'accueil fonctionnelle et presentable pour le développement front-end.

## Choix techniques

- Bootstrap 5 via **ImportMap** (cohérent avec la stack du projet, tout en local)
- Variables CSS custom via `:root` pour une charte graphique unifiée
- `clamp()` pour les tailles de police responsive sans media queries
- Données dynamiques depuis Doctrine (pas de fixtures codées en dur dans le template)
