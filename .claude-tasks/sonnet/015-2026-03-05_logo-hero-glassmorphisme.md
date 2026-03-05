# 015 — Logo, image de fond et effet glassmorphisme

**Date** : 2026-03-05
**Modèle** : Sonnet
**Justification** : Modifications CSS avancées (glassmorphisme, backgrounds), templates Twig, création asset SVG

## Fichiers modifiés / créés

- `public/images/logo-cf2m.svg` — Nouveau logo SVG circulaire (bâtiment stylisé + anneau cyan)
- `assets/styles/app.css` — Refonte `.cf2m-hero` + ajout `.cf2m-hero-glass`
- `templates/base.html.twig` — Logo image dans la navbar
- `templates/home/index.html.twig` — Contenu hero encapsulé dans `.cf2m-hero-glass`

## Résumé

### Logo SVG (`public/images/logo-cf2m.svg`)
Icône circulaire 80×80px : fond navy (#08111e), anneau cyan, bâtiment stylisé avec fenêtres et porte.
Référencé via `{{ asset('images/logo-cf2m.svg') }}` dans la navbar.

### Background hero
Remplacement du gradient+pattern SVG par :
- `background-color: #06101c` (fallback)
- 3 radial-gradients colorés (blobs bleus/cyan) pour le glassmorphisme
- `url('/images/hero-bg.jpg')` en dernier calque (optionnel : ajouter une photo à cet emplacement)

### Glassmorphisme (`.cf2m-hero-glass`)
- `backdrop-filter: blur(22px) saturate(1.6)`
- `background: rgba(6, 16, 30, 0.50)` semi-transparent
- Bordure `rgba(255,255,255,0.10)` + box-shadow
- Responsive : padding réduit sous 576px

## Résultat
Site répond HTTP 200. Logo visible dans la navbar. Effet glassmorphisme appliqué sur le panel hero.
Pour la photo de fond : déposer `public/images/hero-bg.jpg` (photo sombre de type "mains sur clavier").
