# Tâche : Adaptation visuelle "raffiné white/dark" — tokens + navbar/footer

**Numéro** : 192
**Date** : 2026-08-05
**Modèle utilisé** : Sonnet
**Justification du modèle** : Restyle de templates/CSS en réutilisant l'architecture Bootstrap + CSS variables existante — au-delà de la retouche cosmétique triviale (analyse préalable du modèle de tokens, propagation cohérente, correction d'un bug de contraste), mais sans décision d'architecture globale.
**Complexité** : Moyenne
**Fichiers concernés** : `assets/styles/app.css`, `templates/base.html.twig`

## Contexte nécessaire
Un mockup généré par claude.ai/design a été reçu dans `datas/Site raffiné whitedark mode/` : nouvelle charte visuelle mais livré comme une refonte complète hors-Bootstrap, avec un modèle de données fictif et seulement 4 pages sur la vingtaine du site réel (voir `.claude/plans/foamy-sniffing-petal.md`).

Décision utilisateur : garder Bootstrap, ne perdre aucune fonctionnalité, ne pas importer le modèle de données fictif. Le mockup sert uniquement de référence visuelle (palette, typo, rayons) portée dans l'architecture CSS existante — pas de copier-coller de templates. Étapes 1 (tokens) et 2 (navbar/footer) du plan sont couvertes par cette tâche.

## Objectif
Rapprocher la palette actuelle (`assets/styles/app.css`) de celle du mockup, puis restyler navbar/footer, sans toucher au markup Bootstrap ni à la logique Twig dynamique (`nav_formations()`, `nav_pages()`, `pending_revisions_count()`, `is_granted`).

## Contraintes
- Garder toutes les variables `--bs-*` et la mécanique dark/light existante (`[data-theme]` sur `<html>`).
- Ne pas introduire les champs fictifs du mockup (`formation.name`, `.price`, etc.) ni ses routes inexistantes (`app_pixel_and_co`, `app_dashboard`).
- Aucune modification de `src/` (contrôleurs, entités, formulaires).

## Critères d'acceptation
- [x] `lint:twig` OK sur `base.html.twig`
- [x] Aucune ancienne valeur de couleur résiduelle (`00b4d8`, `48cae4`, `08111e`, `05111f`, anciens triplets RGB)
- [x] Accolades CSS équilibrées
- [x] Page d'accueil répond `200` et affiche toujours `cf2m-navbar`/`cf2m-hero`
- [x] Contraste navbar/footer light mode vérifié et corrigé (voir Résultat)

## Résultat
**Tokens (`assets/styles/app.css` `:root` + `[data-theme="light"]`)** :
- `--cf2m-dark` `#08111e→#050e18`, `--cf2m-cyan` `#00b4d8→#3cc8e6`, `--cf2m-cyan-light` `#48cae4→#7fdcef` (accent aligné sur la maquette).
- `--bs-link-color` référence désormais `var(--cf2m-cyan-light)` au lieu d'un hex dupliqué.
- Fond du `body` aligné sur `#050e18`.
- Propagation mécanique (`replace_all`) des anciens triplets RGB codés en dur dans les `rgba()` de glows/ombres/bordures : `0, 180, 216 → 60, 200, 230` (~62 occurrences), `8, 17, 30 → 5, 14, 24` (5 occurrences) — pour garder tous les effets (pas seulement les usages via `var()`) cohérents avec le nouvel accent.
- Ajout de `--cf2m-cyan: #0072a3;` dans le bloc `[data-theme="light"]` existant (meilleur contraste sur fond blanc, réutilise une teinte déjà présente ailleurs dans le fichier).

**Bug détecté et corrigé pendant l'étape navbar/footer** : le `--cf2m-cyan` plus foncé du light mode cassait la lisibilité dans la navbar et le footer, qui restent volontairement sombres même en light mode (comportement documenté dans le CSS : "fond toujours sombre → texte blanc"). Ajout d'un override scopé :
```css
[data-theme="light"] .cf2m-navbar,
[data-theme="light"] .cf2m-footer { --cf2m-cyan: #3cc8e6; }
```

**Navbar/footer (`assets/styles/app.css`, `templates/base.html.twig`)** :
- Liens de nav desktop : soulignement animé (`::after`) remplacé par un fond en pastille arrondie au survol/actif (`background: rgba(60,200,230,.12)`, `border-radius: 0.6rem`), aligné sur le traitement déjà existant en mobile.
- Coins des menus déroulants (`0.75rem → 1rem`).
- `templates/base.html.twig` : classe utilitaire Bootstrap du footer `py-4 → py-5`.

## Suite
Étapes 3 à 7 du plan restent à faire (page d'accueil, formation/show, contact, login, vérification des pages non couvertes) — voir `.claude/plans/foamy-sniffing-petal.md` et la mémoire projet associée.