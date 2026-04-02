# 097 — Extension Twig plain_text

**Modèle** : Sonnet
**Justification** : Création d'une extension Twig + mise à jour template

## Fichiers modifiés
- `src/Twig/TextExtension.php` (nouveau)
- `templates/home/index.html.twig`

## Résumé
Le contenu SunEditor est stocké avec des entités HTML encodées (`&lt;h2&gt;` au lieu de `<h2>`).
Le filtre Twig `striptags` ne voyait pas ces balises encodées et les affichait telles quelles.

Création du filtre `plain_text` qui :
1. `html_entity_decode()` → décode les entités (`&lt;h2&gt;` → `<h2>`)
2. `strip_tags()` → supprime les balises HTML
3. `preg_replace('/\s+/', ' ')` → normalise les espaces/sauts de ligne

Utilisation dans le template : `formation.description|plain_text` à la place de `striptags|trim`.

## Résultat
Les balises HTML (`<h2>`, `<p>`, etc.) ne s'affichent plus dans les cartes formations.
