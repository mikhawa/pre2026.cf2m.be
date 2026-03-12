# 054 — Footer : alignement logo + colonne Nos activités

**Date** : 2026-03-12 10:00
**Fichiers modifiés** :
- `templates/base.html.twig`
- `assets/styles/app.css`

## Résumé
Refonte du footer en 3 colonnes Bootstrap :
1. **Gauche** : logo SVG aligné avec "Centre de Formation" via flexbox (`d-flex align-items-center`)
2. **Centre** : liste des pages "Nos activités" (si `_nav_pages` non vide), avec titre "NOS ACTIVITÉS"
3. **Droite** : copyright inchangé

Ajout de `.cf2m-footer-link` dans `app.css` pour le style hover des liens.

## Raison
Amélioration visuelle du footer : cohérence d'alignement et enrichissement avec les liens de navigation.
