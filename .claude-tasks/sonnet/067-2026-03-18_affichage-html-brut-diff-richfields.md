# 067 — Affichage HTML brut (5 lignes max) dans le diff historique

**Modèle** : Sonnet
**Justification** : Modification de service métier (RevisionService)

## Fichiers modifiés
- `src/Service/RevisionService.php`

## Résumé
Dans `buildHistoryDiffHtml()`, les champs riches (`description`, `content`) affichaient auparavant le texte brut via `strip_tags` + truncate 120 chars dans une `<div>`.

Nouveau comportement :
- `formatRichFieldForDiff()` insère des sauts de ligne après les balises de bloc (`</p>`, `</h1-6>`, `</li>`, `</div>`, `</ul>`, `</ol>`, `</blockquote>`, `<br>`, etc.)
- Retourne les 5 premières lignes obtenues + un `…` si tronqué
- Affiché dans un `<pre>` avec `white-space: pre-wrap` → balises HTML visibles en tant que texte

## Résultat
En cliquant sur "modifié ▾", on voit jusqu'à 5 lignes de HTML source (avec `<p>`, `<strong>`, etc. lisibles), côte à côte avant/après en rouge/vert.
