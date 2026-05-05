# Frontend — CF2m

## Stack
| Couche | Technologie |
|--------|-------------|
| CSS framework | Bootstrap 5 |
| JS | Stimulus + Turbo (ImportMap, pas de bundler) |
| Templates | Twig |
| Icônes | À définir (Bootstrap Icons recommandé) |

## Palette de couleurs
Extraite de la maquette d'accueil (`datas/`).

| Rôle | Couleur | Hex |
|------|---------|-----|
| Primaire (fond header, boutons) | Bleu foncé | `#0d2b4e` (à confirmer) |
| Accent (chiffres clés, CTA) | Vert clair | `#4fcf8a` (à confirmer) |
| Texte principal | Blanc sur fond sombre / Noir sur fond clair | `#ffffff` / `#1a1a1a` |
| Fond hero | Overlay sombre sur photo | `rgba(0,0,0,0.5)` |
| Bouton CTA principal | Bleu moyen | `#1a5eb8` (à confirmer) |

> **TODO** : extraire les codes hex exacts depuis la charte graphique officielle ou le fichier Figma/XD.

## Structure des templates Twig
```
templates/
├── base.html.twig                # Layout principal (head, navbar, footer)
├── home/
│   └── index.html.twig           # Page d'accueil
├── formation/
│   ├── index.html.twig           # Liste des formations
│   └── show.html.twig            # Détail d'une formation
├── works/
│   ├── index.html.twig           # Liste des travaux
│   └── show.html.twig            # Détail d'un travail
├── page/
│   └── show.html.twig            # Pages statiques (mentions légales, etc.)
├── contact/
│   └── index.html.twig           # Formulaire de contact
├── security/
│   └── login.html.twig           # Page de connexion
└── components/                   # Composants Twig réutilisables
    ├── _navbar.html.twig
    ├── _footer.html.twig
    ├── _formation_card.html.twig
    └── _flash_messages.html.twig
```

## Structure de base.html.twig
Blocs Bootstrap à définir :
- `{% block title %}` — titre de la page
- `{% block meta %}` — balises meta SEO
- `{% block stylesheets %}` — CSS supplémentaire par page
- `{% block body %}` — contenu principal
- `{% block javascripts %}` — JS supplémentaire par page

## Composants de la page d'accueil
D'après la maquette :

### Navbar
- Logo CF2m (haut gauche)
- Liens : Nos Formations · Nous contacter · Nos activités
- **Position sticky universelle** : `position: sticky; top: 0; z-index: 1030` sur toutes les pages sans exception (home, login, register, pages internes)
- Les compensations de hauteur (`padding-top: 70px`) ont été supprimées des templates auth et de `.cf2m-hero`
- Bootstrap : `navbar navbar-expand-lg`

### Section Hero
- Image de fond plein écran avec overlay sombre (fichiers WebP optimisés — voir `docs/architecture/seo-performances.md`)
- Titre : "FORMATIONS PROFESSIONNELLES des métiers du numérique"
- Chiffres clés mis en avant avec couleur accent :
  - **100%** gratuit
  - **80%** de pratique
  - **1600 h** de cours
- Bouton CTA : "NOS FORMATIONS" (Bootstrap `btn btn-primary`)
- **Colonne droite** : photo de groupe (`.jpg` + `.webp`, 900×675 px, format 4:3) dans un cadre arrondi (`border-radius: 16px`) — remplace l'ancien portrait circulaire

### Footer
- À définir

## Conventions Bootstrap 5
- Breakpoints : `sm` (576px), `md` (768px), `lg` (992px), `xl` (1200px)
- Grille : `container` + `row` + `col-*`
- Boutons principaux : `btn btn-primary`
- Alertes flash : `alert alert-success/danger/warning/info`
- Formulaires : `form-control`, `form-label`, `form-select`

## Variables CSS personnalisées
À définir dans `assets/styles/app.css` :
```css
:root {
    --cf2m-primary:   #0d2b4e; /* à confirmer */
    --cf2m-accent:    #4fcf8a; /* à confirmer */
    --cf2m-cta:       #1a5eb8; /* à confirmer */
}
```

## Stimulus controllers
Répertoire : `assets/controllers/`

| Controller | Rôle |
|------------|------|
| `suneditor_controller.js` | Active SunEditor sur les textarea du back-office |
| `navbar_controller.js` | Gestion scroll (navbar transparente → opaque) |

## ImportMap
Packages à déclarer dans `importmap.php` :
- `bootstrap` (CSS + JS)
- `@hotwired/stimulus`
- `@hotwired/turbo`
- `suneditor` (back-office uniquement)

## SEO et performances

Voir `docs/architecture/seo-performances.md` pour :
- Meta descriptions par page (blocs Twig surchargeables)
- `robots.txt`
- Conversion WebP des images (`hero-bg`, `formations-bg`, `hero-portrait`)
- Chargement non bloquant des Google Fonts
- Lazy loading des images hors-écran

## TODO
- [ ] Confirmer les codes hex exacts avec la charte graphique
- [ ] Héberger Google Fonts en local (`@font-face` + `.woff2`) pour supprimer la dépendance réseau
- [ ] Optimiser `logo-cf2m-blanc.svg` (136 KB → < 10 KB via SVGO)
