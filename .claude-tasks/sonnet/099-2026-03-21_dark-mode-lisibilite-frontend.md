# 099 — Dark mode : lisibilité frontend complète

**Modèle** : Sonnet
**Justification** : CSS + templates, pas d'architecture

## Fichiers modifiés
- `assets/styles/app.css`
- `templates/formation/show.html.twig`
- `templates/page/show.html.twig`
- `templates/profil/index.html.twig`

## Résumé
1. **Bootstrap CSS vars** dans `:root` : `--bs-body-color`, `--bs-card-color`, `--bs-link-color`, etc. → dark mode
2. **Surcharges globales** : `.text-muted`, `small`, `a`, `.alert` → couleurs lisibles sur fond sombre
3. **`.cf2m-muted-label`** : nouvelle classe pour remplacer les `style="color: #7a95aa"` inline
4. **`.cf2m-formation-description` + `.cf2m-page-content`** : styles complets pour contenu SunEditor `|raw` (h1-h6, p, ul, li, strong, em, a, table, blockquote)
5. **Templates** : remplacement des `style="color: #7a95aa"` inline dans formation/show, page/show et profil/index
