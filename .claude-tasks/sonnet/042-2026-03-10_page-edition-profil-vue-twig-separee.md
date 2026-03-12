# 042 — Page d'édition du profil en vue Twig séparée

**Date** : 2026-03-10
**Modèle** : Sonnet
**Justification** : Refactoring controller + templates pour remplacer le toggle JS par une navigation classique

## Fichiers modifiés / créés
- `src/Controller/ProfileController.php` — nouvelle action `edit()` + route `/profil/modifier`
- `templates/profil/index.html.twig` — simplifié : lecture seule, lien vers la page d'édition
- `templates/profil/edit.html.twig` — créé : formulaire d'édition dédié

## Résumé
- `index()` : ne gère plus de formulaire, render simple de la vue lecture
- `edit()` : reprend toute la logique form/flush/redirect, render `profil/edit.html.twig`
- La vue index contient un lien `<a href="{{ path('app_profile_edit') }}">Modifier mon profil</a>`
- La vue edit contient un lien retour `← Mon profil`
- Plus aucun JS de toggle, plus de localStorage, plus de collapse Bootstrap

## Résultat
Navigation claire entre deux pages distinctes, aucune ambiguïté visuelle.
