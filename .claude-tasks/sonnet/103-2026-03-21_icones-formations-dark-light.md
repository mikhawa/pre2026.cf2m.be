# 103 — Icônes thématiques formations dark/light mode

**Modèle** : Sonnet
**Justification** : Template Twig + CSS + assets statiques

## Fichiers modifiés
- `templates/home/index.html.twig` — ajout du mapping slug→icône + `<img>` dans card-header
- `assets/styles/app.css` — `.cf2m-formation-icon` + override `[data-theme="light"]`
- `public/images/formation-icons/` — 8 SVG copiés depuis `datas/images-2026-03-12/`

## Mapping formations → icônes
| Slug | Icône (dark = blanche) |
|---|---|
| aventure-digitale | download-computer.svg |
| animateur-multimedia | movie-speaker.svg |
| technicien-pc-reseaux | network-tree.svg |
| digital-designer | responsive-design.svg |
| web-developer-full-stack | collect-computer.svg |
| cheques-tic | multimedia.svg |
| illustration-numerique | design-ideas.svg |
| marketing-digital-reseaux-sociaux | network-globe.svg |

## Technique dark/light
- Dark mode : icônes SVG blanches (`stroke="#fff"` / `fill="#fff"`) — visibles sur fond sombre
- Light mode : `filter: invert(1) brightness(0.25) sepia(0.4) saturate(3) hue-rotate(190deg)` → teinte navy sur fond `#c4dff0`
- Le mapping est dans le template Twig (dict), sans toucher au champ `logo` VichUploader
