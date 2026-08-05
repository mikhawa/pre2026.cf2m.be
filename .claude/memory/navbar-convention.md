---
name: navbar-convention
description: La navbar (et le footer) restent toujours sur fond sombre, même en light mode — ne jamais laisser le backdrop-filter actif sur fond blanc
metadata:
  type: project
---

## Convention navbar (couleur uniforme)
Sur les pages intérieures (non-home, non-login), la navbar doit avoir `background: var(--cf2m-dark)` + `backdrop-filter: none` pour rester visuellement identique à son apparence sur la home (qui flotte sur fond sombre). Ne jamais laisser le `backdrop-filter` actif sur fond blanc.

Corollaire découvert pendant [[restyle-white-dark]] : la navbar et le footer restent volontairement sombres même en light mode — toute variable d'accent retintée pour le light mode doit être vérifiée dans ces deux zones (override scopé si besoin, ex. `[data-theme="light"] .cf2m-navbar, .cf2m-footer { --cf2m-cyan: ... }`).
