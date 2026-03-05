# 018 — Refonte menu / navbar

**Date** : 2026-03-05
**Modèle** : Sonnet
**Justification** : CSS layout navbar + positionnement hero — travail de design/structure

## Fichiers modifiés

- `assets/styles/app.css` — Navbar (positionnement, liens, bouton connexion), hero padding-top 100vh
- `templates/base.html.twig` — Logo seul, liens centrés (mx-auto), libellés maquette, suppression sticky-top Bootstrap

## Résumé des changements

### Positionnement navbar
- **page-home** : `position: absolute` — flotte par-dessus le hero, widget 100vh inclut la navbar
- **pages intérieures** : `position: sticky; top: 0` + fond opaque 0.92
- Suppression de la classe Bootstrap `sticky-top` du HTML (gérée en CSS selon contexte)

### Logo
- Icône circulaire seule (42px), sans texte "CF2m" à côté — fidèle à la maquette

### Liens
- `mx-auto` au lieu de `me-auto` → liens centrés dans la navbar
- Libellés mis à jour : "Nos formations", "Nous contacter", "Nos activités"
- Suppression du lien "Accueil" (logo fait office de lien home)
- Soulignement cyan animé au hover (::after scaleX)

### Bouton Connexion
- Style pill (border-radius: 2rem) + bordure blanche semi-transparente
- Hover : couleur cyan (cohérent avec le design)

### Hero
- `padding-top: calc(70px + 4rem)` pour que le contenu apparaisse sous la navbar absolue
- `min-height: 100vh` → remplit toute la fenêtre

## Résultat
HTTP 200 sur / et /connexion. Navbar flotte sur le hero, hero remplit 100vh.
