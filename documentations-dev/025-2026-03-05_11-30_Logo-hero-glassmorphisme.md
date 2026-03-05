# 025 — Logo CF2m, image de fond et glassmorphisme

**Date** : 2026-03-05 11:30
**Branche** : FirstFrontend

## Fichiers modifiés / créés

| Fichier | Changement |
|---------|-----------|
| `public/images/logo-cf2m.svg` | Nouveau logo SVG circulaire CF2m |
| `assets/styles/app.css` | Refonte du hero + styles glassmorphisme |
| `templates/base.html.twig` | Ajout `<img>` logo dans la navbar |
| `templates/home/index.html.twig` | Contenu hero encapsulé dans `.cf2m-hero-glass` |

## Raison

Correspondance visuelle avec la maquette `datas/exemple-accueil.png` : logo circulaire en haut à gauche, fond sombre avec effet de profondeur, panneau glassmorphisme sur le contenu du hero.

## Détails des changements

### Logo (`public/images/logo-cf2m.svg`)
SVG 80×80 : cercle navy, anneau cyan, bâtiment stylisé (toit, murs, fenêtres cyan, porte blanche).
Ajouté dans la navbar via `{{ asset('images/logo-cf2m.svg') }}` à côté du texte "CF2m".

### Hero background
- Suppression : gradient très opaque (0.97) + pattern SVG data:URI
- Remplacement : 3 radial-gradients bleus/cyan formant des blobs colorés
- `url('/images/hero-bg.jpg')` : photo optionnelle à déposer dans `public/images/`
- `::before` et `::after` repositionnés (haut droite / bas gauche) pour encadrer le panel

### Glassmorphisme (`.cf2m-hero-glass`)
```css
background: rgba(6, 16, 30, 0.50);
backdrop-filter: blur(22px) saturate(1.6);
border: 1px solid rgba(255, 255, 255, 0.10);
border-radius: 1.5rem;
```

## Note
Pour ajouter la photo de fond : déposer une image sombre (ex. mains sur clavier) à `public/images/hero-bg.jpg`.
