---
name: restyle-white-dark
description: Restyle visuel "raffiné white/dark" en 7 étapes à partir d'un mockup claude.ai/design — terminé le 2026-08-05, méthode de vérification par capture d'écran
metadata:
  type: project
---

## Restyle visuel "raffiné white/dark" — TERMINÉ ✅ (2026-08-05)

### Origine
Un mockup généré par claude.ai/design a été reçu dans `datas/Site raffiné whitedark mode/`. Analyse : c'est une refonte complète hors-Bootstrap avec un modèle de données fictif (`formation.name`, `.price`, `.duration`...) et seulement 4 pages sur la vingtaine du site réel — inutilisable tel quel (routes inexistantes `app_pixel_and_co`/`app_dashboard`, `ContactType` incompatible, Turnstile absent, `.claude-tasks/192-...md` documente l'analyse complète).

### Décision utilisateur
Garder Bootstrap intégralement, ne perdre aucune fonctionnalité existante, ne pas importer le modèle de données fictif. Le mockup sert uniquement de **référence visuelle** (palette, typo, rayons) portée dans l'architecture CSS existante — pas de copier-coller de templates.

### Plan (7 étapes, toutes terminées)
1. ✅ Tokens couleur dans `assets/styles/app.css` (tâche `.claude-tasks/sonnet/192-...md`)
2. ✅ Navbar + footer
3. ✅ Page d'accueil (hero, cartes formations, partenaires) — tâche `.claude-tasks/sonnet/193-...md`
4. ✅ Page formation/show — tâche `.claude-tasks/sonnet/194-...md` (héritait déjà des styles de l'étape 3 ; entité `Formation` réelle n'a pas les champs du mockup — duration/price/modules — donc pas de reproduction de sa structure hero+meta+modules, juste vérification + fix d'une régression : flèche en double sur "Retour aux formations")
5. ✅ Contact (formulaire réel + Turnstile) — tâche `.claude-tasks/sonnet/195-...md` (page déjà fonctionnelle et cohérente ; juste harmonisation des derniers rayons carrés — `.cf2m-input`, `.cf2m-btn-submit` — en pill, comme le reste du site)
6. ✅ Login (formulaire réel + Turnstile + liens forgot-password/register) — tâche `.claude-tasks/sonnet/196-...md` (composant `.cf2m-login-*` partagé par login/forgot-password/register/reset-password/2FA : un seul jeu de règles CSS harmonisées en pill couvre tout le parcours)
7. ✅ Vérification visuelle des pages non couvertes — tâche `.claude-tasks/sonnet/197-...md` (page générique, works/show déjà cohérentes ; profil/reset-password/2FA vérifiés par lecture de code, faute d'identifiants de fixtures valides pour se connecter en dev)

**Découverte annexe hors scope** : la route publique `app_public_profile` (`/utilisateur/{id}`) expose des données de profil sans authentification — signalé par l'utilisateur comme problématique. Voir [[bug-profil-public-accessible]]. Non corrigé (hors périmètre du restyle CSS).

### Ce qui a changé (étapes 1-2)
- Accent cyan : `#00b4d8 → #3cc8e6` (dark), `#48cae4 → #7fdcef` (hover), propagé aussi dans les `rgba()` codés en dur (glows/ombres/bordures, pas seulement les `var(--cf2m-cyan)`).
- Fond sombre : `#08111e → #050e18`.
- Accent light mode assombri à `#0072a3` pour le contraste sur fond blanc — **piège identifié** : ça casse la lisibilité dans `.cf2m-navbar`/`.cf2m-footer`, qui restent volontairement sombres même en light mode (voir [[navbar-convention]]). Corrigé par un override scopé `[data-theme="light"] .cf2m-navbar, .cf2m-footer { --cf2m-cyan: #3cc8e6; }`. **À garder en tête pour toute nouvelle variable retintée en light mode : vérifier si elle retombe dans une zone "toujours sombre".**
- Nav-link desktop : soulignement `::after` remplacé par fond pastille au survol (`rgba(60,200,230,.12)`, `border-radius: .6rem`), aligné sur le style déjà utilisé en mobile.
- Footer : `py-4 → py-5`.

### Ce qui a changé (étape 3 — accueil)
Première passe (rayons seuls) jugée insuffisante par l'utilisateur ("aucune ressemblance au thème envoyé") → **toujours comparer par capture d'écran avant de considérer un restyle "terminé"**, ne pas se fier au diff CSS seul. Méthode qui a débloqué la suite : rendu HTML statique du mockup (ses propres templates + `app.css` + données d'exemple du README) capturé via Playwright, comparé côte à côte avec le site réel.

- Rayons "pill" (`999px`) : `.cf2m-hero-btn`, `.cf2m-card .badge-status`, `.cf2m-partner-item`, `.cf2m-card .btn-warning`.
- `.cf2m-card` : rayon `0.75rem → 1.25rem` (toutes les cartes du site, pas seulement l'accueil).
- `.cf2m-partners` : `border-top` ajouté en dark mode.
- **Hero** : panneau vitré (`.cf2m-hero-glass`) supprimé (`padding:0`, plus de fond/blur/bordure/ombre) — le texte repose directement sur `public/images/hero-bg.jpg` (asset existant mais plus référencé nulle part avant cette tâche) via un scrim `linear-gradient` propre à chaque thème. `text-shadow` de sécurité sur `h1`/`.lead`. Filet `border-top` ajouté au-dessus de `.cf2m-hero-stats`.
- **Cartes formations** : CTA secondaire ("En savoir plus") converti en lien texte + flèche `→` (plus de bouton). CTA prioritaire ("S'inscrire", formations en recrutement) **volontairement gardé en bouton plein** — différenciation UX délibérée (action prioritaire vs secondaire), absente du mockup mais jugée meilleure. **Décision produit tranchée avec l'utilisateur** : la couleur par formation (`colorPrimary`/`colorSecondary`, feature back-office réelle) est **conservée**, contrairement au panneau uniforme du mockup.
- `.page-home #formations` et `.cf2m-partners` : padding vertical `clamp(3rem, 6vw, 6rem)` (plus généreux).
- Bug corrigé au passage : texte du bouton CTA hero invisible en light mode (conflit de spécificité entre `[data-theme="light"] a` et `.cf2m-hero-btn` — la règle générique `a` gagnait sur la classe). Toujours vérifier la spécificité quand un sélecteur `[data-theme="light"] <balise>` générique coexiste avec une classe `.cf2m-*` sur le même élément.

### Convention de vérification utilisée
`docker compose exec -T php php bin/console lint:twig`, `curl` (code HTTP), vérification manuelle des accolades CSS, **+ vérification visuelle possible via Playwright en passant par le `npx` Windows (pas le conteneur Linux)** : `cmd.exe /c "cd /d C:\<dossier> && npx playwright install chromium"` puis un script `.mjs` avec `import { chromium } from 'playwright'` ciblant `http://localhost:8085` (port Docker publié, accessible depuis Windows). Écrire les fichiers directement sur `/mnt/c/...` (pas via l'outil Write avec un chemin `C:\...`, qui crée un fichier au nom littéral côté Linux). C'est la voie qui fonctionne — le conteneur Linux, lui, n'a toujours pas les libs système pour un Chromium natif (`libnspr4.so` manquant).
