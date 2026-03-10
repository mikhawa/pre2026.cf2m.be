# 038 — Couleur primaire sur les encadrés de formation (page accueil)

**Date** : 2026-03-10
**Modèle** : Haiku (modification CSS + Twig ciblée)

## Fichiers modifiés
- `assets/styles/app.css`
- `templates/home/index.html.twig`

## Résumé

Utilisation de `colorPrimary` de chaque formation comme couleur de départ du gradient du `card-header`.

### Technique : CSS custom property
- CSS : `background: linear-gradient(135deg, var(--card-color-primary, var(--cf2m-dark)) 0%, var(--cf2m-navy-md) 100%)`
- Twig : `style="--card-color-primary: {{ formation.colorPrimary }}"` sur la `div.card` (uniquement si non null)
- Fallback propre : si `colorPrimary` est null, le gradient existant est conservé tel quel
