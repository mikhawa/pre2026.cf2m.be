# 019 — Liens vers les formations (FormationController + page détail)

**Date** : 2026-03-07
**Modèle** : Sonnet (controller + routes + templates)
**Justification** : Création d'un controller métier avec routing, template de détail et mise à jour de plusieurs templates existants.

## Fichiers modifiés / créés

- `src/Controller/FormationController.php` — créé : route `GET /formation/{slug}` → `app_formation_show`
- `src/Repository/FormationRepository.php` — ajout méthode `findOneBySlug(string $slug): ?Formation`
- `templates/formation/show.html.twig` — créé : page de détail d'une formation
- `templates/base.html.twig` — lien navbar dropdown : `href="#"` → `path('app_formation_show', {slug: formation.slug})`
- `templates/home/index.html.twig` — lien carte "En savoir plus" : `href="#"` → `path('app_formation_show', {slug: formation.slug})`

## Résumé

Les boutons "En savoir plus" sur les cartes de la home et les items du dropdown "Nos formations" dans la navbar pointent désormais vers la route `/formation/{slug}`.

Le controller retourne une 404 si le slug est inconnu ou si la formation n'est pas publiée.

## Résultat

Liens fonctionnels vers une page de détail avec fil d'ariane, description complète et bouton "Retour aux formations".
