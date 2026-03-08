# 028 - Menu "Nos activités" : dropdown des pages publiées

**Modèle** : Sonnet
**Justification** : Controller + Repository + Twig extension + template + navbar
**Date** : 2026-03-08

## Fichiers modifiés / créés
- `src/Repository/PageRepository.php`
- `src/Twig/NavigationExtension.php`
- `src/Controller/PageController.php` (nouveau)
- `templates/page/show.html.twig` (nouveau)
- `templates/base.html.twig`

## Résumé
- `findAllPublished()` dans PageRepository
- `nav_pages()` dans NavigationExtension (même pattern cache mémoire que nav_formations)
- PageController avec route `/activites/{slug}` → `app_page_show`
- Template `page/show.html.twig` calqué sur `formation/show.html.twig`
- "Nos activités" converti en dropdown Bootstrap dans base.html.twig

## Résultat
Les pages publiées apparaissent dans le menu déroulant "Nos activités" avec lien vers `/activites/{slug}`.
