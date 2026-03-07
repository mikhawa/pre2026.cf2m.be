# 017 — Rapprochement design avec la maquette exemple-accueil.png

**Date** : 2026-03-05
**Modèle** : Sonnet
**Justification** : Modifications CSS significatives (hero, navbar, glassmorphisme) + templates

## Fichiers modifiés

- `assets/styles/app.css` — Navbar glassmorphique, hero 85vh, glassmorphisme subtil, portrait circulaire
- `templates/home/index.html.twig` — Colonne droite portrait, stats "1600 h", body class page-home
- `templates/base.html.twig` — body class block + data-page

## Résumé des écarts corrigés

| Élément | Avant | Après |
|---------|-------|-------|
| Navbar | Fond solide `#08111e` | Glassmorphisme rgba(6,14,26,0.55) + blur(18px) |
| Hero height | 65vh | 85vh |
| Overlay hero | Gradients opaques cachant la photo | Overlay directionnel léger (gauche 52% → droite 10%) |
| Glassmorphisme contenu | rgba(6,16,30,0.50) très sombre | rgba(5,15,32,0.38) plus transparent |
| Colonne droite | Vide | Portrait circulaire avec anneau cyan + placeholder CSS |
| 3e stat | Nb dynamique formations | "1600 h de cours" (statique comme la maquette) |
| body class | Absent | `page-home` sur accueil, `inner` sur autres pages |

## Résultat
HTTP 200. Design nettement plus proche de la maquette.
Manque encore : `public/images/hero-bg.jpg` + `public/images/hero-portrait.jpg`
