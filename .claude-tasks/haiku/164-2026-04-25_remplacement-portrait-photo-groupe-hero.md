# 164 — Remplacement portrait circulaire par photo de groupe dans le hero

**Modèle** : Haiku
**Justification** : Remplacement d'image simple, ajustement CSS mineur

## Fichiers modifiés
- `public/images/hero-portrait.jpg` — photo de groupe redimensionnée (900×675, 117 Ko)
- `public/images/hero-portrait.webp` — copie JPG (WebP non disponible dans le container GD)
- `templates/home/index.html.twig` — suppression de l'anneau SVG, attributs img mis à jour (900×675, alt "Stagiaires CF2m")
- `assets/styles/app.css` — `.cf2m-hero-portrait-outer` et `.cf2m-hero-portrait` adaptés pour format paysage (border-radius: 16px au lieu de 50%, dimensions 100%/max-width: 520px)

## Résumé
Source : `datas/20250513_151917.jpg` (Samsung Galaxy S25+, 4000×3000, 2,2 Mo).
Redimensionnée via PHP/GD à 900×675 dans le container Docker.
Cadre circulaire remplacé par un cadre rectangulaire arrondi (16px) adapté au format paysage.
L'anneau SVG décoratif (`cercle-contour-blanc.svg`) a été retiré du template.
