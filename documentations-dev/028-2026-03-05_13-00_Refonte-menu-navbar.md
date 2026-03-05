# 028 — Refonte menu / navbar

**Date** : 2026-03-05 13:00
**Branche** : FirstFrontend

## Fichiers modifiés

| Fichier | Changement |
|---------|-----------|
| `assets/styles/app.css` | Positionnement absolu sur home, liens centrés, bouton pill |
| `templates/base.html.twig` | Logo seul, `mx-auto`, libellés maquette, sans `sticky-top` |

## Changements détaillés

### Positionnement contextuel (CSS)
```css
/* Sur la home : overlay du hero */
.page-home .cf2m-navbar {
    position: absolute; left:0; right:0; top:0; z-index:1030;
}
/* Sur pages intérieures : sticky normal */
body:not(.page-home) .cf2m-navbar {
    position: sticky; top:0; z-index:1030;
    background: rgba(6,14,26,0.92);
}
```

### Hero 100vh
```css
.cf2m-hero {
    padding: calc(70px + 4rem) 0 5rem;
    min-height: 100vh;
}
```
Le hero remonte derrière la navbar absolue.

### HTML — structure simplifiée
| Avant | Après |
|-------|-------|
| Logo + texte "CF2m" | Logo seul (42px) |
| Liens alignés à gauche (`me-auto`) | Liens centrés (`mx-auto`) |
| "Accueil", "Formations", "À propos", "Contact", "CF2d" | "Nos formations", "Nous contacter", "Nos activités", "CF2d" |
| Bouton connexion rect avec bordure cyan | Bouton pill (border-radius:2rem) bord blanc |

### Liens avec animation hover
```css
.cf2m-navbar .nav-link::after {
    /* trait cyan animé via scaleX(0→1) */
    background: var(--cf2m-cyan);
    transform: scaleX(0);
    transition: transform 0.2s ease;
}
```
