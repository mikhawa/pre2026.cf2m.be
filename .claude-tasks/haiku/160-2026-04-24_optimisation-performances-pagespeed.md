---
modèle: haiku (Sonnet pour coordination)
justification: Optimisations statiques HTML/CSS/images — aucune logique métier
fichiers:
  - assets/styles/app.css
  - templates/base.html.twig
  - templates/home/index.html.twig
  - public/images/hero-bg.webp (créé)
  - public/images/hero-bg-mobile.webp (créé)
  - public/images/formations-bg.webp (créé)
  - public/images/hero-portrait.webp (créé)
résumé: |
  Optimisations PageSpeed mobile :
  1. Conversion d'images en WebP via Docker ImageMagick :
     - hero-bg.jpg (2.7 MB) → hero-bg.webp (387 Ko, −86%) + hero-bg-mobile.webp (67 Ko)
     - formations-bg.jpg (356 Ko) → formations-bg.webp (140 Ko, −61%)
     - hero-portrait.jpg (224 Ko) → hero-portrait.webp (142 Ko, −37%)
  2. CSS responsive pour hero-bg : mobile → hero-bg-mobile.webp (scroll), desktop → hero-bg.webp (fixed)
  3. Google Fonts : @import retiré du CSS, remplacé par <link preconnect> + media="print" onload dans base.html.twig (chargement non-bloquant)
  4. hero-portrait : balise <picture> avec <source type="image/webp"> + fetchpriority="high"
  5. formations-bg.jpg → formations-bg.webp dans l'inline style du template home
  6. loading="lazy" ajouté sur les icônes formations et logos partenaires
résultat: ✅
---
