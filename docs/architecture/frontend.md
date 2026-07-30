# Frontend — CF2m

## Stack
| Couche | Technologie |
|--------|-------------|
| CSS framework | Bootstrap 5 |
| JS | Stimulus + Turbo (ImportMap, pas de bundler) |
| Templates | Twig |
| Polices | Google Fonts (`Outfit` en titres, `DM Sans` en corps de texte) — chargement non bloquant, voir `docs/architecture/seo-performances.md` |

## Palette de couleurs
Définie dans `assets/styles/app.css` (`:root`), mode sombre par défaut avec bascule mode clair (voir plus bas).

| Variable CSS | Rôle | Hex |
|------|---------|-----|
| `--cf2m-dark` | Fond hero / navbar | `#08111e` |
| `--cf2m-navy` | Fond alternatif | `#0d1e35` |
| `--cf2m-navy-md` | Variante mid | `#142f52` |
| `--cf2m-cyan` | Accent principal (liens, CTA, icônes) | `#00b4d8` |
| `--cf2m-cyan-light` | Cyan clair / hover | `#48cae4` |
| `--cf2m-gold` | Accent secondaire (stats, labels) | `#f4c430` |
| `--cf2m-light` | Fond clair de section | `#f0f6fa` |
| `--cf2m-white` | Blanc | `#ffffff` |
| `--cf2m-muted` | Texte atténué (sur fond sombre) | `rgba(255,255,255,0.6)` |

En mode clair (`[data-theme="light"]`), les variables Bootstrap (`--bs-body-color`, `--bs-emphasis-color`, etc.) sont réécrites — les couleurs `--cf2m-*` restent identiques, c'est le contraste texte/fond qui change.

## Mode sombre / clair — toggle

Géré par le Stimulus controller `theme_controller.js` :
- Thème par défaut : `dark`, persisté dans `localStorage` (clé `cf2m-theme`)
- Bascule via `data-theme` sur `<html>`, piloté entièrement en CSS (pas de classes JS)
- De nombreuses règles `[data-theme="light"] ...` dans `app.css` réécrivent les couleurs pour le mode clair (attention aux règles trop génériques qui peuvent écraser des couleurs de composants spécifiques — voir `documentations-dev/117-...` pour un cas vécu sur les boutons de partage)

## Structure des templates Twig (état réel)
```
templates/
├── base.html.twig                # Layout principal (head, navbar, footer, toggle thème)
├── _matomo.html.twig             # Partial Matomo (voir docs/architecture/analytics.md)
├── home/
│   └── index.html.twig           # Page d'accueil (hero, formations, activités, partenaires)
├── formation/
│   └── show.html.twig            # Détail d'une formation + works publiés
├── works/
│   └── show.html.twig            # Détail d'un travail (sidebar partage réseaux sociaux)
├── page/
│   └── show.html.twig            # Pages statiques (activités, mentions légales, etc.)
├── contact/
│   ├── index.html.twig
│   └── success.html.twig
├── security/
│   ├── login.html.twig
│   ├── reset_password.html.twig
│   └── two_factor.html.twig      # Saisie du code 2FA
├── registration/
│   └── register.html.twig
├── profil/
│   ├── index.html.twig           # Profil connecté
│   ├── edit.html.twig            # Édition profil (avatar, bio, liens)
│   ├── public.html.twig          # Profil public d'un utilisateur
│   └── utilisateurs.html.twig    # Liste des membres (ROLE_FORMATEUR+)
├── admin/
│   ├── dashboard.html.twig
│   ├── revisions-en-attente.html.twig
│   └── formation/stagiaires.html.twig  # (voir docs/architecture/easyadmin.md)
├── bundles/EasyAdminBundle/      # Overrides de layout EasyAdmin (ex: Matomo back-office)
└── emails/                       # Templates d'emails transactionnels
```

> Pas de dossier `components/` avec partials `_navbar` / `_footer` / `_flash_messages` : la navbar et le footer sont directement dans `base.html.twig`. Il n'y a pas non plus de vue `formation/index.html.twig` séparée — la liste des formations est intégrée à `home/index.html.twig`.

## Structure de base.html.twig
Blocs Twig disponibles (surchargeables par page) :
- `{% block title %}` — titre de la page
- `{% block meta_description %}` / `{% block meta_robots %}` — SEO (voir `docs/architecture/seo-performances.md`)
- `{% block meta_og %}` — Open Graph / Twitter Card (surchargé par ex. dans `works/show.html.twig`)
- `{% block stylesheets %}` — CSS supplémentaire par page
- `{% block body %}` — contenu principal
- `{% block javascripts %}` — JS supplémentaire par page

## Composants de la page d'accueil

### Navbar
- Logo CF2m (haut gauche), liens principaux, bascule thème
- **Position sticky universelle** : `position: sticky; top: 0; z-index: 1030` sur toutes les pages sans exception (home, login, register, pages internes)
- Bootstrap : `navbar navbar-expand-lg navbar-dark cf2m-navbar`

### Section Hero
- Image de fond plein écran avec overlay sombre (fichiers WebP optimisés — voir `docs/architecture/seo-performances.md`)
- Titre : "FORMATIONS PROFESSIONNELLES des métiers du numérique"
- Chiffres clés mis en avant avec couleur accent (`--cf2m-gold`) :
  - **100%** gratuit
  - **80%** de pratique
  - **1600 h** de cours
- Bouton CTA : "NOS FORMATIONS" (Bootstrap `btn btn-primary`)
- **Colonne droite** : photo de groupe (`.jpg` + `.webp`, 900×675 px, format 4:3) dans un cadre arrondi (`border-radius: 16px`)

### Footer (`cf2m-footer`, dans `base.html.twig`)
- Colonne gauche : logo (`logo-cf2m-blanc.svg`) + « Centre de Formation 2M »
- Colonne centre : « Nos activités » — liste des `Page` publiées (`_nav_pages`)
- Colonne droite : copyright dynamique (`{{ 'now'|date('Y') }}`)

## Conventions Bootstrap 5
- Breakpoints : `sm` (576px), `md` (768px), `lg` (992px), `xl` (1200px)
- Grille : `container` + `row` + `col-*`
- Boutons principaux : `btn btn-primary`
- Alertes flash : `alert alert-success/danger/warning/info`
- Formulaires : `form-control`, `form-label`, `form-select`

## Stimulus controllers
Répertoire : `assets/controllers/`

| Controller | Rôle |
|------------|------|
| `theme_controller.js` | Bascule dark/light mode, persistance `localStorage` |
| `suneditor_controller.js` | Active SunEditor sur les textarea du back-office |
| `avatar_crop_controller.js` | Recadrage client de l'avatar (80×80) avant upload VichUploader |
| `csrf_protection_controller.js` | Protection CSRF sur formulaires spécifiques (ex: AJAX) |
| `hello_controller.js` | Contrôleur de démonstration Symfony (scaffold par défaut) |

## ImportMap
Packages déclarés dans `importmap.php` :
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
- [ ] Optimiser `logo-cf2m-blanc.svg` (136 KB → < 10 KB via SVGO)
- [ ] Héberger Google Fonts en local (`@font-face` + `.woff2`) pour supprimer la dépendance réseau