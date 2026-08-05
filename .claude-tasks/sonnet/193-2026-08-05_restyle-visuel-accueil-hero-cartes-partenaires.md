# Tâche : Adaptation visuelle "raffiné white/dark" — page d'accueil (hero, cartes, partenaires)

**Numéro** : 193
**Date** : 2026-08-05
**Modèle utilisé** : Sonnet
**Justification du modèle** : Suite directe de la tâche 192 (restyle CSS réutilisant l'architecture Bootstrap + tokens existants) — même complexité (moyenne), pas de décision d'architecture.
**Complexité** : Moyenne
**Fichiers concernés** : `assets/styles/app.css`

## Contexte nécessaire
Étape 3 du plan de restyle "raffiné white/dark" (voir `.claude-tasks/sonnet/192-2026-08-05_restyle-visuel-tokens-navbar-footer.md` et mémoire projet). Étapes 1 (tokens) et 2 (navbar/footer) déjà terminées. Le mockup de référence (`datas/Site raffiné whitedark mode/symfony/`) utilise systématiquement des rayons "pill" (`--cf2m-r-pill: 999px`) sur boutons, badges et chips, et un rayon de carte généreux (`--cf2m-r: 20px`), contre des rayons plus carrés dans le CSS actuel.

Décision utilisateur (rappel) : garder Bootstrap et le markup Twig existant, ne pas importer le modèle de données fictif du mockup. Seule la référence visuelle (rayons, dans ce cas) est portée dans `assets/styles/app.css`.

## Objectif
Rapprocher hero, cartes de formations et section partenaires de la page d'accueil de l'esthétique du mockup, sans toucher au markup Twig ni à `src/`.

## Contraintes
- Aucune modification de `templates/` ni `src/`.
- Garder toutes les variables `--cf2m-*` et la mécanique dark/light existante.

## Critères d'acceptation
- [x] Accolades CSS équilibrées
- [x] `lint:twig` OK (aucun template touché)
- [x] Page d'accueil répond `200`
- [x] Vérification visuelle dark + light (Playwright/Chromium via npx côté Windows, `localhost:8085`)
- [x] Aucune régression de contraste introduite

## Résultat
**`assets/styles/app.css`** :
- `.cf2m-card` : `border-radius: 0.75rem → 1.25rem` (rayon de carte plus généreux, cohérent avec `--cf2m-r: 20px` du mockup — impacte toutes les cartes du site, pas seulement l'accueil, pour cohérence visuelle globale)
- `.cf2m-hero-btn` : `border-radius: 0.4rem → 999px` (pill)
- `.cf2m-card .badge-status` : `border-radius: 0.3rem → 999px` (utilisé sur `formation/show.html.twig`)
- `.cf2m-card .btn-outline-secondary` (CTA carte formation "S'inscrire"/"En savoir plus") : `border-radius: 0.6rem → 999px`
- `.cf2m-partner-item` : `border-radius: 0.6rem → 999px`
- `.cf2m-partners` : ajout `border-top: 1px solid rgba(255,255,255,0.08)` en dark mode (séparateur de section déjà présent en light mode, manquant en dark)

**Bug détecté et corrigé pendant la vérification visuelle** (hors scope initial mais dans le même fichier/composant) : en light mode, le texte du bouton CTA hero "Nos formations" était invisible (couleur identique au fond). Cause : conflit de spécificité CSS — `[data-theme="light"] a { color: #0072a3; }` (sélecteur attribut + balise, spécificité 0,0,1,1) l'emportait sur `.cf2m-hero-btn { color: var(--cf2m-dark); }` (simple classe, spécificité 0,0,1,0). Correctif : ajout de `[data-theme="light"] .cf2m-hero-btn { color: var(--cf2m-dark); }` pour égaliser puis gagner par ordre de source.

## Suite
Étapes 4 à 7 du plan restent à faire (page formation/show, contact, login, vérification des pages non couvertes) — voir mémoire projet associée.
