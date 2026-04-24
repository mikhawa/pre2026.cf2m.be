# 105 — SEO : meta descriptions et optimisation performances PageSpeed

**Date** : 2026-04-24  
**Déclencheur** : Audit PageSpeed Insights (mobile) signalant l'absence de meta description et des problèmes de performance (LCP, render-blocking resources)

---

## 1. Ajout des meta descriptions et robots.txt

### Problèmes signalés
- Absence de `<meta name="description">` sur toutes les pages
- Pas de `robots.txt` — comportement d'indexation implicite

### Fichiers modifiés

**`templates/base.html.twig`**  
Ajout de deux blocs Twig dans `<head>` :
- `{% block meta_description %}` — description générique par défaut, surchargeable par chaque page
- `<meta name="robots" content="{% block meta_robots %}index, follow{% endblock %}">` — autorisation explicite d'indexation

**`templates/home/index.html.twig`**  
Override `meta_description` : description dédiée à l'accueil.

**`templates/formation/show.html.twig`**  
Override avec `formation.descriptionCourte` (fallback : `formation.description|striptags|slice(0,155)`).

**`templates/page/show.html.twig`**  
Override avec `page.content|striptags|slice(0,155)`.

**`templates/works/show.html.twig`**  
Override avec `work.description|striptags|slice(0,155)`.

**`templates/contact/index.html.twig`**  
Override avec description statique adaptée au formulaire de contact.

**`public/robots.txt`** (création)  
```
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /profil/modifier
Disallow: /reinitialiser-mot-de-passe/
Disallow: /connexion
Disallow: /inscription
```

---

## 2. Optimisation des performances PageSpeed (mobile)

### Problèmes identifiés

| Problème | Impact estimé |
|---|---|
| `hero-bg.jpg` 2.7 MB en background CSS | LCP mobile catastrophique (~2–3 s) |
| `formations-bg.jpg` 356 KB | Chargement inutilement lourd |
| `hero-portrait.jpg` 224 KB | Non optimisé |
| Google Fonts via `@import` dans le CSS | Render-blocking (bloque le rendu) |
| Pas de `preconnect` Google Fonts | Latence DNS + TLS supplémentaire |
| Images partenaires/icônes sans `loading="lazy"` | Chargement inutile hors-écran |
| `background-attachment: fixed` sur mobile | Bug connu iOS/Android |

### Conversions WebP (via Docker ImageMagick)

| Fichier original | Taille | Fichier WebP | Taille | Gain |
|---|---|---|---|---|
| `hero-bg.jpg` | 2 700 KB | `hero-bg.webp` | 387 KB | **−86 %** |
| — | — | `hero-bg-mobile.webp` (800px max, q70) | 67 KB | **−98 %** |
| `formations-bg.jpg` | 356 KB | `formations-bg.webp` | 140 KB | **−61 %** |
| `hero-portrait.jpg` | 224 KB | `hero-portrait.webp` | 142 KB | **−37 %** |

Commande utilisée :
```bash
docker run --rm --entrypoint="" \
  -v /chemin/public/images:/images \
  dpokidov/imagemagick \
  magick /images/source.jpg -quality 75 /images/sortie.webp
```

### Fichiers modifiés

**`assets/styles/app.css`**
- Suppression du `@import url('https://fonts.googleapis.com/...')` (render-blocking)
- `body` en dark mode : `hero-bg-mobile.webp` par défaut (mobile), `hero-bg.webp` à partir de 992px via `@media`
- `background-attachment: scroll` sur mobile (fix bug iOS/Android), `fixed` conservé uniquement sur desktop
- Même logique pour `[data-theme="light"] .cf2m-hero`

**`templates/base.html.twig`**
- `<link rel="preconnect" href="https://fonts.googleapis.com">`
- `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>`
- Google Fonts chargé via `media="print" onload="this.media='all'"` (chargement non bloquant)
- `<noscript>` fallback pour navigateurs sans JS

**`templates/home/index.html.twig`**
- `formations-bg.jpg` → `formations-bg.webp` dans l'inline style de la section formations
- `hero-portrait` : `<img>` remplacé par `<picture><source type="image/webp">` + `fetchpriority="high"`
- `loading="lazy"` ajouté sur les icônes de formations
- `loading="lazy"` ajouté sur les logos des partenaires

### Point restant (hors code)

- **`logo-cf2m-blanc.svg` (136 KB)** : le SVG contient des données base64 embarquées (export Illustrator). À réexporter proprement depuis Illustrator ou à passer par [SVGO](https://svgo.dev/) pour réduire à < 10 KB.
- Les polices Google Fonts restent hébergées chez Google. Héberger en local (`@font-face` + fichiers `.woff2`) supprimerait la dépendance réseau tierce.

---

## Tâches associées

- `.claude-tasks/haiku/159-2026-04-24_meta-description-robots-seo.md`
- `.claude-tasks/haiku/160-2026-04-24_optimisation-performances-pagespeed.md`
