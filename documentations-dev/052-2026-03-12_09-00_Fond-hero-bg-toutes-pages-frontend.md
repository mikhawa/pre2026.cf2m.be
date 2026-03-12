# 052 — Fond hero-bg.jpg sur toutes les pages frontend

**Date** : 2026-03-12 09:00
**Fichier modifié** : `assets/styles/app.css`

## Raison
L'utilisateur souhaitait que `public/images/hero-bg.jpg` soit visible en fond sur toutes les pages frontend (contact, formation, profil, works, activités), comme sur la page d'accueil.

## Changements

### body — fond global fixe
```css
body {
    background-color: #05111f;
    background-image:
        radial-gradient(...),
        url('/images/hero-bg.jpg');
    background-attachment: fixed;
    background-size: auto, auto, cover;
    background-position: center, center, 65% center;
}
```

### Pages intérieures — ajustements lisibilité
- `.cf2m-section-title` → `color: var(--cf2m-white)` sur fond sombre
- `.text-muted` → `var(--cf2m-muted) !important`
- Breadcrumbs → couleurs claires (liens `rgba(255,255,255,0.65)`, actif `rgba(255,255,255,0.4)`)
- `.cf2m-card` → `background: rgba(255,255,255,0.88)` pour maintenir la lisibilité du texte sombre

### Page works/show
- `.cf2m-work-body` → `background: transparent` (était `var(--cf2m-light)`)
