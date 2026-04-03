# 128 — Formations "Recrutement" visibles sur le frontend

**Modèle** : Sonnet (logique métier + templates multiples)
**Date** : 2026-04-03

## Justification
Les formations avec le statut `recruiting` devaient être affichées sur le site au même titre que les formations `published`, mais en étant mises en avant (triées en premier, badge visible).

## Fichiers modifiés

| Fichier | Modification |
|---|---|
| `src/Repository/FormationRepository.php` | `findAllPublished()` inclut désormais `recruiting` + `published`, avec `recruiting` trié en premier via `HIDDEN statusOrder` |
| `src/Controller/FormationController.php` | La condition d'accès à la page de détail accepte `published` ET `recruiting` |
| `templates/base.html.twig` | Badge "Recrutement" dans le dropdown navbar |
| `templates/home/index.html.twig` | Badge + bouton "S'inscrire" mis en avant (couleur warning) sur les cartes en recrutement |
| `templates/formation/show.html.twig` | Badge dynamique : "Recrutement en cours" (warning) vs "Ouverte" (badge-status) |

## Résumé des changements

- **Ordre d'affichage** : `recruiting` → `published` (via `CASE WHEN` Doctrine HIDDEN)
- **Page d'accueil** : badge jaune "Recrutement en cours" + bouton "S'inscrire" jaune (au lieu de "En savoir plus" gris)
- **Navbar** : badge compact "Recrutement" à côté du titre dans le dropdown
- **Page détail** : badge contextuel selon le statut réel de la formation
- **Accès** : les URLs `/formation/{slug}` pour les formations `recruiting` sont maintenant accessibles
