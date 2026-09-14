# 199 — Fond white mode des pages intérieures (thème claude.ai/design)

**Modèle utilisé** : Sonnet

**Justification** : modification CSS ciblée d'une règle existante (couleur de fond),
pas de création d'architecture ni de logique métier — complexité modérée (controllers/services/CSS).

## Contexte

L'utilisateur a partagé un design claude.ai/design ("CF2M Site", export récupéré dans
`datas/canvas-claude/`) et a demandé que le fond des pages autres que l'accueil, en white
mode, s'inspire de ce thème. L'export ne contient pas de nouvelle image de fond (le hero
référence `/images/hero-bg.jpg`, déjà utilisée dans le projet) : l'apport concret est la
palette de fond nettement plus claire (`--cf2m-bg:#f5f7f9` / `--cf2m-panel:#ffffff`) que
l'actuel bleu saturé `#c4dff0`.

## Analyse

Dans `assets/styles/app.css`, `#c4dff0` en light mode n'était utilisé qu'à 4 endroits :
- `body` (fond universel — visible sur **toutes** les pages qui n'ont pas de fond dédié :
  fiche formation, contact, dashboard, profil, etc.)
- `#formations` (section accueil)
- `.cf2m-hero` (hero accueil, sous le voile + photo)
- `.cf2m-login-section` (page connexion)

Seul `body` correspond aux « autres pages » (accueil et login ont déjà leur propre fond
dédié) : c'est la seule règle modifiée.

## Fichiers modifiés

- `assets/styles/app.css` (ligne ~1697-1701) : `[data-theme="light"] body` —
  `background-color: #c4dff0` → `#f5f7f9` (valeur reprise telle quelle de `--cf2m-bg`
  dans le thème claude.ai/design partagé).

## Résumé

Les pages intérieures (hors accueil/login) affichent désormais un fond gris quasi-blanc
raffiné au lieu du bleu ciel saturé, cohérent avec la palette du nouveau thème partagé.
Les cartes (déjà en `#ffffff`) restent inchangées et gagnent en lisibilité sur ce fond
plus clair.

## Résultat

Changement appliqué et vérifié par lecture du CSS (une seule occurrence touchée,
les 3 autres `#c4dff0` — accueil et login — volontairement laissées intactes).
Vérification visuelle en navigateur non effectuée (stack Docker dev non démarrée dans
cet environnement de session en arrière-plan) — à confirmer par l'utilisateur au prochain
`docker compose up` + `asset-map:compile`.
