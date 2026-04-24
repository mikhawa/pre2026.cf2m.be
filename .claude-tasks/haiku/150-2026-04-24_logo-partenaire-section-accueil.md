# 150 — Affichage logo partenaire dans la section "Nos Partenaires" de l'accueil

**Modèle** : Haiku
**Justification** : Modification template Twig + CSS uniquement

## Fichiers modifiés

- `templates/home/index.html.twig` — affichage logo si existant, sinon nom texte
- `assets/styles/app.css` — style `.cf2m-partner-logo` dark + light mode

## Résumé

Si un partenaire a un logo uploadé, l'image remplace le texte dans la carte.
En dark mode : logo rendu blanc (filter invert) avec teinte cyan au survol.
En light mode : logo affiché couleurs naturelles.
Fallback : nom texte si pas de logo.
