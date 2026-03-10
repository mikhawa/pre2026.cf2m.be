# 046 — Couleur primaire sur les encadrés de formation (accueil)

**Date** : 2026-03-10 13:30
**Fichiers modifiés** :
- `assets/styles/app.css`
- `templates/home/index.html.twig`

## Résumé

Les encadrés de formation sur la page d'accueil utilisent désormais le champ `colorPrimary` de chaque formation comme couleur de départ du gradient de l'en-tête de carte.

## Implémentation

**CSS** (`app.css`) — remplacement du gradient fixe par une CSS custom property avec fallback :
```css
.cf2m-card .card-header {
    background: linear-gradient(135deg,
        var(--card-color-primary, var(--cf2m-dark)) 0%,
        var(--cf2m-navy-md) 100%);
}
```

**Twig** (`home/index.html.twig`) — injection conditionnelle de la variable CSS :
```twig
<div class="card cf2m-card"
     {% if formation.colorPrimary %} style="--card-color-primary: {{ formation.colorPrimary }}"{% endif %}>
```

Si `colorPrimary` est null, le gradient existant (`var(--cf2m-dark)`) est utilisé comme fallback — aucun changement visuel pour les formations sans couleur définie.
