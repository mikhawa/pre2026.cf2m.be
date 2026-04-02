# 102 — Light mode : contrastes & ombrages

**Modèle** : Sonnet
**Justification** : CSS pur, pas d'architecture

## Fichiers modifiés
- `assets/styles/app.css`

## Résumé
### Hero glassmorphisme (principal problème)
- `[data-theme="light"] .cf2m-hero-glass` : glass → **verre blanc opaque** (`rgba(255,255,255,0.88)`) + `backdrop-filter` saturé + 3 couches d'ombre portée (6/24/64px)
- `[data-theme="light"] .cf2m-hero::before` : overlay photo renforcé (0.55→0.38→0.18) pour contraster le verre blanc
- Textes dans la glass : `h1` → `#0d1e35` navy, `.lead` → `#2e4a62`, stat-label → `#4a6878`

### Ombrages généraux
- Cards : triple box-shadow (2+8+28px, teinte bleue sombre)
- Section formations : `box-shadow: inset` + `border-top: 2px`; bg plus saturé `#c4dff0`
- Partners : `inset` shadow + `border-top: 2px`; partner-items : ombre plus marquée
- Works sidebar + description card : ombres portées profondes
