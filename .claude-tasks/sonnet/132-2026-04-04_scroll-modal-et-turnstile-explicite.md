---
modèle: Sonnet
justification: Correction de deux bugs liés à la modal Bootstrap : layout flex pour le scroll, et rendu explicite Turnstile
date: 2026-04-04
---

# Tâche 132 — Scroll modal + Turnstile rendu explicite

## Fichiers modifiés

- `templates/formation/show.html.twig`

## Problèmes corrigés

### 1. Scroll absent dans la modal

**Cause** : la balise `<form>` s'insère entre `.modal-content` (flex container Bootstrap) et `.modal-body`. Bootstrap attend ses enfants directs (header/body/footer), pas un `<form>` intermédiaire. La contrainte de hauteur pour déclencher le scroll sur `.modal-body` ne se propageait jamais.

**Correction** :
- `<form>` : `display:flex; flex-direction:column; flex:1 1 auto; min-height:0; overflow:hidden`
- `.modal-body` : `flex:1 1 auto; min-height:0; overflow-y:auto; -webkit-overflow-scrolling:touch`

### 2. Turnstile échoue / ne s'affiche pas

**Cause** : Cloudflare Turnstile ne rend pas le widget dans un élément `display:none` (la modal Bootstrap au chargement). Le token `cf-turnstile-response` restait vide à la soumission → vérification serveur toujours en échec.

**Correction** :
- Script chargé avec `?render=explicit` (pas d'auto-render)
- Le widget est rendu via `turnstile.render()` dans l'événement `shown.bs.modal`
- Si le widget existe déjà (modal ré-ouverte), `turnstile.reset()` génère un nouveau token
- Retry automatique toutes les 100ms si le script Turnstile n'est pas encore chargé

## Résumé

Le scroll apparaît désormais dès que le contenu dépasse la hauteur disponible (incluant l'apparition du widget Turnstile). Le widget Turnstile se charge et génère un token valide à chaque ouverture de la modal.
