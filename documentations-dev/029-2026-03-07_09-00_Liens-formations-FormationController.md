# 029 — Liens vers les formations

**Date** : 2026-03-07 09h00
**Tâche** : Création des liens vers les pages de détail des formations

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/FormationController.php` | Créé |
| `src/Repository/FormationRepository.php` | Ajout `findOneBySlug()` |
| `templates/formation/show.html.twig` | Créé |
| `templates/base.html.twig` | Mise à jour liens navbar |
| `templates/home/index.html.twig` | Mise à jour liens cartes |

## Résumé

- Création du `FormationController` avec la route `GET /formation/{slug}` (nom `app_formation_show`)
- La méthode `show()` retourne 404 si le slug est inconnu ou si la formation n'est pas publiée
- Page de détail avec fil d'ariane, description complète (HTML brut via `|raw`) et bouton d'inscription (placeholder)
- Liens actifs dans le dropdown de navigation et sur les cartes de la page d'accueil

## Raison

Les liens "En savoir plus" et les items du menu dropdown pointaient sur `href="#"` (placeholder).
