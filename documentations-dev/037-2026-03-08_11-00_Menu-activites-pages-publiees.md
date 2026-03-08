# 037 — Menu "Nos activités" : dropdown des pages publiées

**Date** : 2026-03-08 11:00
**Branche** : Navigation

## Fichiers modifiés / créés
- `src/Repository/PageRepository.php` — ajout de `findAllPublished()`
- `src/Twig/NavigationExtension.php` — ajout de `nav_pages()` + injection `PageRepository`
- `src/Controller/PageController.php` — **nouveau** : route `app_page_show` `/activites/{slug}`
- `templates/page/show.html.twig` — **nouveau** : template d'affichage d'une page
- `templates/base.html.twig` — remplacement du lien simple "Nos activités" par un dropdown

## Résumé des changements

### PageRepository
Nouvelle méthode `findAllPublished()` : retourne les pages avec `status = 'published'`, triées par titre ASC.

### NavigationExtension
- Injection de `PageRepository` en plus de `FormationRepository`
- Nouvelle fonction Twig `nav_pages()` avec cache mémoire (même pattern que `nav_formations()`)

### PageController
Route `GET /activites/{slug}` → `app_page_show`. Retourne 404 si la page n'existe pas ou n'est pas publiée.

### base.html.twig
- Ajout de `{%- set _nav_pages = nav_pages() -%}`
- "Nos activités" transformé en `dropdown` Bootstrap identique au dropdown "Nos formations"
- Affiche le compteur `cf2m-nav-count` si des pages sont disponibles

## Raison
Les pages publiées (vie étudiante, partenaires, etc.) doivent être accessibles depuis le menu principal sans avoir à les lier manuellement.
