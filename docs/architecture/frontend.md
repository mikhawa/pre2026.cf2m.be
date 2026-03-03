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
- Fond semi-transparent sur hero, opaque en scroll
- Bootstrap : `navbar navbar-expand-lg`

### Section Hero
- Image de fond plein écran avec overlay sombre
- Titre : "FORMATIONS PROFESSIONNELLES des métiers du numérique"
- Chiffres clés mis en avant avec couleur accent :
  - **100%** gratuit
  - **80%** de pratique
  - **1600 h** de cours
- Bouton CTA : "NOS FORMATIONS" (Bootstrap `btn btn-primary`)
- Image ronde d'une personne (côté droit)

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

## TODO
- [ ] Confirmer les codes hex exacts avec la charte graphique
- [ ] Créer `base.html.twig` avec les blocs Bootstrap
- [ ] Créer le composant `_navbar.html.twig` (transparente + scroll)
- [ ] Créer la section hero avec les chiffres clés
- [ ] Définir et créer le footer
- [ ] Déclarer tous les packages dans `importmap.php`
- [ ] Créer `assets/styles/app.css` avec les variables CSS CF2m
