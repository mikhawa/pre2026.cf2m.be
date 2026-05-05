# SEO et performances — CF2m

**Dernière mise à jour** : 2026-04-24 (audit PageSpeed Insights mobile)

---

## 1. Meta descriptions

### Structure dans `base.html.twig`

Deux blocs Twig ajoutés dans `<head>` :

```twig
{% block meta_description %}
    <meta name="description" content="Centre de formation CF2m — formations professionnelles des métiers du numérique.">
{% endblock %}

<meta name="robots" content="{% block meta_robots %}index, follow{% endblock %}">
```

### Surcharges par page

| Template | Contenu |
|---|---|
| `home/index.html.twig` | Description dédiée à l'accueil |
| `formation/show.html.twig` | `formation.descriptionCourte` (fallback : `description\|striptags\|slice(0,155)`) |
| `page/show.html.twig` | `page.content\|striptags\|slice(0,155)` |
| `works/show.html.twig` | `work.description\|striptags\|slice(0,155)` |
| `contact/index.html.twig` | Description statique |

---

## 2. robots.txt

Fichier : `public/robots.txt`

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

## 3. Images — conversion WebP

Conversions réalisées via Docker + ImageMagick :

```bash
docker run --rm --entrypoint="" \
  -v /chemin/public/images:/images \
  dpokidov/imagemagick \
  magick /images/source.jpg -quality 75 /images/sortie.webp
```

| Fichier original | Taille | Fichier WebP | Taille | Gain |
|---|---|---|---|---|
| `hero-bg.jpg` | 2 700 KB | `hero-bg.webp` | 387 KB | −86 % |
| — | — | `hero-bg-mobile.webp` (800px max, q70) | 67 KB | −98 % |
| `formations-bg.jpg` | 356 KB | `formations-bg.webp` | 140 KB | −61 % |
| `hero-portrait.jpg` | 224 KB | `hero-portrait.webp` | 142 KB | −37 % |

Utilisation dans `app.css` (dark mode) :

```css
/* mobile par défaut */
background-image: url('/images/hero-bg-mobile.webp');

/* desktop à partir de 992px */
@media (min-width: 992px) {
    background-image: url('/images/hero-bg.webp');
}
```

`background-attachment: scroll` sur mobile (fix bug iOS/Android), `fixed` conservé uniquement sur desktop.

---

## 4. Google Fonts — chargement non bloquant

Dans `base.html.twig` :

```html
<!-- Preconnect -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Chargement non bloquant -->
<link rel="stylesheet" href="https://fonts.googleapis.com/..."
      media="print" onload="this.media='all'">
<noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/...">
</noscript>
```

Le `@import url('...')` précédent dans `app.css` a été supprimé (render-blocking).

---

## 5. Lazy loading images hors-écran

`loading="lazy"` ajouté sur :
- Icônes de formations (section accueil)
- Logos des partenaires (section accueil)

L'image hero (`hero-portrait`) utilise `fetchpriority="high"` et est servie via `<picture><source type="image/webp">`.

---

## 6. Points d'amélioration restants

- **`logo-cf2m-blanc.svg` (136 KB)** : contient des données base64 embarquées (export Illustrator). À réexporter proprement ou passer par [SVGO](https://svgo.dev/) pour réduire à < 10 KB.
- **Google Fonts hébergés localement** : utiliser `@font-face` + fichiers `.woff2` pour supprimer la dépendance réseau tierce.
