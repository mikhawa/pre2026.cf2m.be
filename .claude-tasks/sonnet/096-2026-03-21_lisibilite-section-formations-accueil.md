# 096 — Lisibilité section formations accueil

**Modèle** : Sonnet
**Justification** : Modifications CSS + template Twig, pas d'architecture complexe

## Fichiers modifiés
- `templates/home/index.html.twig`
- `assets/styles/app.css`

## Résumé
1. **Suppression de la date de publication** dans les cartes formations (bloc `publishedAt` retiré du template)
2. **Titre "Nos formations"** : ajout de règles `.page-home #formations .cf2m-section-label/title` → `color: white` sur fond sombre
3. **Cartes formations** : ajout de `.page-home #formations .cf2m-card` avec `background: rgba(255,255,255,0.92)` pour rendre le texte lisible sur fond sombre

## Résultat
- Le titre et le label "Catalogue" sont désormais visibles en blanc sur la section sombre
- Les cartes ont un fond blanc opaque rendant le texte `#4a6070` lisible
- La date de publication n'est plus affichée
