# 050 — Page d'édition du profil en vue Twig séparée

**Date** : 2026-03-10 16:30

## Raison
Le toggle JS entre deux blocs sur la même page causait des problèmes visuels et de fiabilité. Remplacé par deux pages distinctes avec navigation classique.

## Fichiers modifiés / créés

### src/Controller/ProfileController.php
- `index()` : supprimé tout le code formulaire, render simple
- Nouveau `edit()` : route `/profil/modifier` (name: `app_profile_edit`), gère le formulaire, redirige vers `app_profile` après succès

### templates/profil/index.html.twig
- Supprimé : formulaire, collapse, tout le JS, localStorage
- Ajouté : lien `<a href="{{ path('app_profile_edit') }}">Modifier mon profil</a>`

### templates/profil/edit.html.twig (nouveau)
- Formulaire d'édition complet (avatar, biographie, 3 liens)
- Lien retour vers `app_profile`
- Prévisualisation avatar en JS (minimaliste, ciblé sur son seul `id`)
