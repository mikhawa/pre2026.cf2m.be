# 114 — Partage sur les réseaux sociaux dans les pages Works

**Date :** 2026-06-10 14:26  
**Commits :** `631f3d3` (boutons partage) · `23fc760` (meta OG/Twitter)  
**Branche :** `feature/24-prepa-for-design`

---

## Contexte

Les pages de détail d'un Work (`/formation/{slug}/works/{slug}`) n'avaient aucun mécanisme de partage sur les réseaux sociaux, ni de balises Open Graph permettant un aperçu enrichi lors d'un partage de lien.

---

## Fonctionnalité ajoutée

### 1. Boutons de partage dans la sidebar

Un nouveau bloc **"Partager"** a été ajouté dans la sidebar de `templates/works/show.html.twig`, entre le bloc "Formation" et le bouton "Retour".

**Réseaux supportés :**

| Réseau | URL de partage |
|--------|---------------|
| Facebook | `https://www.facebook.com/sharer/sharer.php?u=…` |
| X / Twitter | `https://twitter.com/intent/tweet?url=…&text=…` |
| LinkedIn | `https://www.linkedin.com/sharing/share-offsite/?url=…` |
| WhatsApp | `https://wa.me/?text=…` |

Les URLs utilisent les variables Twig `pageUrl` (URL absolue via `url()`) et `pageTitle` (`work.title ~ ' — CF2m'`), encodées via le filtre `url_encode`.

Tous les liens s'ouvrent dans un nouvel onglet (`target="_blank" rel="noopener noreferrer"`).

### 2. Balises meta OG et Twitter Card

Le bloc `{% block meta_og %}` a été ajouté dans `templates/base.html.twig` (en amont du bloc `{% block stylesheets %}`), ce qui permet à chaque page enfant de surcharger les métadonnées Open Graph.

`templates/works/show.html.twig` définit ce bloc avec :

| Balise | Valeur |
|--------|--------|
| `og:type` | `article` |
| `og:url` | URL absolue de la page Works |
| `og:title` | Titre du Work + " — CF2m Centre de Formation" |
| `og:description` | 155 premiers caractères de la description (sans HTML) |
| `og:image` | Logo de la formation (Vich) ou image hero par défaut |
| `og:site_name` | "CF2m Centre de Formation" |
| `twitter:card` | `summary_large_image` |
| `twitter:title` | idem og:title |
| `twitter:description` | idem og:description |
| `twitter:image` | idem og:image |

---

## CSS ajouté (`assets/styles/app.css`)

```css
.cf2m-work-share-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.cf2m-work-share-btn {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.82rem;
    font-weight: 500;
    text-decoration: none;
    transition: opacity 0.2s, transform 0.15s;
    border: none;
    color: #fff;
}
.cf2m-work-share-btn:hover   { opacity: 0.85; transform: translateX(3px); color: #fff; }
.cf2m-work-share-facebook    { background: #1877f2; }
.cf2m-work-share-twitter     { background: #000; }
.cf2m-work-share-linkedin    { background: #0a66c2; }
.cf2m-work-share-whatsapp    { background: #25d366; }
```

Les boutons sont empilés verticalement (`flex-direction: column`), pleins largeur, avec coin arrondi et un léger décalage au survol.

---

## Fichiers modifiés

- `templates/works/show.html.twig`
- `templates/base.html.twig`
- `assets/styles/app.css`

---

## Note sur la compilation des assets

Après ajout de ces styles, un `php bin/console asset-map:compile` est nécessaire si des fichiers compilés existent dans `public/assets/` (environnement où nginx sert les assets statiques directement). En dev pur (sans compilation préalable), Symfony sert `app.css` dynamiquement.
