---
name: dark-light-mode
description: Architecture du toggle dark/light mode (CSS-driven, Stimulus, anti-flash) et palette de couleurs du light mode — terminé 2026-03-21
metadata:
  type: project
---

## Dark/Light mode — TERMINÉ ✅ (2026-03-21)
Tâches 096 à 102. Fichiers principaux modifiés :
- `assets/styles/app.css` — dark mode complet + `[data-theme="light"]` complet
- `assets/controllers/theme_controller.js` — Stimulus controller CSS-driven (localStorage)
- `templates/base.html.twig` — script anti-flash + bouton mobile (pill, centré, hors collapse) + bouton desktop (cercle 36px, dans auth)

### Architecture du toggle
- `html[data-theme]` piloté par CSS : `.cf2m-theme-sun` / `.cf2m-theme-moon` via `display`
- Deux boutons synchronisés automatiquement (desktop `d-none d-lg-flex` + mobile `d-flex d-lg-none mx-auto`)
- Anti-flash inline dans `<head>` : applique le thème avant le rendu

### Couleurs white mode (état d'origine — voir [[restyle-white-dark]] pour les évolutions ultérieures)
- Fond universel : `rgb(196, 223, 240)` = `#c4dff0`
- Hero glass : `rgba(255,255,255,0.88)` + 3 couches d'ombre portée → verre blanc opaque
- Textes hero : `h1` → `#0d1e35`, `.lead` → `#2e4a62`
- Cards : fond blanc + triple box-shadow bleue sombre
- Section formations : `#c4dff0` + `border-top 2px` + inset shadow
- Navbar/dropdowns : texte blanc protégé (`!important`) même en light
