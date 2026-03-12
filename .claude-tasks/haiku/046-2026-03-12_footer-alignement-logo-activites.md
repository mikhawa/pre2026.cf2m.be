---
modèle: haiku
justification: Modification de template Twig et CSS (mise en page footer)
fichiers modifiés:
  - templates/base.html.twig
  - assets/styles/app.css
---

## Résumé
- Passage du footer en 3 colonnes (col-md-4) : logo+texte alignés | Nos activités | copyright
- Logo aligné verticalement avec "Centre de Formation" via flexbox
- Colonne centrale : liste des pages "Nos activités" (réutilise `_nav_pages`)
- Ajout du style `.cf2m-footer-link` avec hover

## Résultat
Footer structuré en 3 colonnes responsives avec les liens Nos activités au centre.
