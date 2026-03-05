# 027 — Rapprochement design avec la maquette

**Date** : 2026-03-05 12:30
**Branche** : FirstFrontend
**Référence** : `datas/exemple-accueil.png`

## Fichiers modifiés

| Fichier | Changement |
|---------|-----------|
| `assets/styles/app.css` | Navbar glass, hero 85vh, overlay directionnel, portrait circulaire |
| `templates/home/index.html.twig` | Col droite portrait + placeholder, stats "1600 h", body class |
| `templates/base.html.twig` | `{% block body_class %}` + `data-page` |

## Écarts corrigés vs maquette

### Navbar glassmorphique
```css
background: rgba(6, 14, 26, 0.55);
backdrop-filter: blur(18px) saturate(1.4);
border-bottom: 1px solid rgba(255, 255, 255, 0.07);
```
Sur pages intérieures : opacité 0.95 (lisibilité).

### Hero : overlay directionnel
```css
background: linear-gradient(110deg,
    rgba(5,15,30, 0.52) 0%,
    rgba(5,15,30, 0.30) 55%,
    rgba(5,15,30, 0.10) 100%);
```
La photo est plus visible à droite (côté portrait), plus sombre à gauche (lisibilité texte).

### Glassmorphisme du panel contenu
- Avant : `rgba(6,16,30, 0.50)` — trop opaque
- Après : `rgba(5,15,32, 0.38)` — plus transparent, effet verre visible

### Colonne droite — portrait circulaire
```html
<div class="cf2m-hero-portrait-wrap col-lg-6 d-none d-lg-flex">
    <div class="cf2m-hero-portrait">...</div>
</div>
```
Cercle 340px max, anneau cyan double, `border-radius: 50%`.
Placeholder CSS jusqu'à dépôt de `public/images/hero-portrait.jpg`.

### Stats mises à jour
| Avant | Après |
|-------|-------|
| `{{ formations\|length }}` formations | `1600 h de cours` |

## Photos manquantes (à fournir par le client)

| Fichier | Usage | Recommandations |
|---------|-------|-----------------|
| `public/images/hero-bg.jpg` | Fond du hero | Photo sombre type "mains sur clavier", format paysage 1920×1080+, dark/moody |
| `public/images/hero-portrait.jpg` | Portrait circulaire | Photo formateur/trice, cadrage buste, fond neutre ou flou |
