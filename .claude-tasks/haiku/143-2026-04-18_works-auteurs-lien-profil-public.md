---
modèle: haiku
justification: Ajout d'un lien sur un élément existant + CSS minimal
fichiers modifiés:
  - templates/works/show.html.twig
  - assets/styles/app.css
---

## Résumé

Dans la page de détail d'un works, chaque auteur est maintenant cliquable et renvoie vers son profil public (`app_public_profile`).

## Changements

- `templates/works/show.html.twig` : le `<li>` auteur est enveloppé dans un `<a href="{{ path('app_public_profile', {id: user.id}) }}">` avec la classe `cf2m-work-author-link`
- `assets/styles/app.css` : ajout de `.cf2m-work-author-link` (flex, color inherit, opacity au hover) + variante light mode
