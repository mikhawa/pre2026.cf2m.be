# 100 — Toggle dark/light mode

**Modèle** : Sonnet
**Justification** : Stimulus controller + CSS + template

## Fichiers modifiés
- `assets/controllers/theme_controller.js` (nouveau)
- `templates/base.html.twig` — script anti-flash + bouton navbar
- `assets/styles/app.css` — `.cf2m-theme-toggle` + `[data-theme="light"]` overrides

## Résumé
1. **Script anti-flash** dans `<head>` : applique le thème depuis localStorage avant le rendu
2. **Stimulus controller `theme`** : toggle dark/light, save localStorage, swap icône soleil/lune
3. **Bouton dans la navbar** : icône SVG soleil (dark → light) / lune (light → dark), cercle 36px, style cohérent avec la navbar
4. **CSS `[data-theme="light"]`** : réinitialise toutes les variables Bootstrap + overrides spécifiques :
   - Body : fond `#eef4f9`, texte dark, pas d'image de fond
   - Cards : blanches avec ombre légère
   - Section formations : fond `#ddeef5`
   - Partenaires : fond dégradé clair
   - Breadcrumbs, contenu riche, Works, textes muted : couleurs sombres adaptées
