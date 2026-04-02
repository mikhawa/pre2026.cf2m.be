# 101 — Toggle thème responsive : desktop droite / mobile centré

**Modèle** : Sonnet
**Justification** : CSS + template Twig, pas d'architecture

## Fichiers modifiés
- `templates/base.html.twig` — ajout bouton desktop (`.cf2m-theme-toggle.d-none.d-lg-flex`) dans la section auth
- `assets/styles/app.css` — icônes CSS-driven + styles `.cf2m-theme-toggle-mobile`

## Résumé
1. **Bouton desktop** (`.d-none.d-lg-flex`) ajouté dans la div auth à droite du collapse navbar, avec SVG soleil + lune
2. **Bouton mobile** (`.d-flex.d-lg-none.mx-auto`, déjà présent hors collapse) avec texte "Mode clair / Mode sombre"
3. **Icônes pilotées par CSS** : `html:not([data-theme="light"]) .cf2m-theme-sun { display: inline-flex }` et inversement — fonctionne sur les deux instances simultanément
4. **`.cf2m-theme-toggle-mobile`** : pill 20px border-radius, gap 6px, padding 6px 14px, fond semi-transparent
