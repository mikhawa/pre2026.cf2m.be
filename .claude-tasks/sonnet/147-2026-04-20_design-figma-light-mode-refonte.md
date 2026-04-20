# 147 — Design Figma 2026 : refonte light mode

**Modèle** : Sonnet (refactoring visuel majeur)
**Date** : 2026-04-20
**Branche** : TestDesignClovis

## Justification
Implémentation d'un design inspiré du prototype Figma (cf2m-dfuse.figma.site) sur la branche TestDesignClovis.

## Fichiers modifiés

- `assets/styles/app.css` — bloc `DESIGN FIGMA 2026` ajouté en fin de fichier (~250 lignes)
- `templates/base.html.twig` — thème par défaut `dark` → `light`, footer restructuré en 4 colonnes
- `templates/home/index.html.twig` — collage hero, 3 nouvelles sections

## Résumé des changements

### CSS (`app.css`)
- Navbar : fond blanc avec ombre légère, liens navy, hover bleu `#1d4ed8`
- Hero : fond bleu solide `#1e40af`, plus de glassmorphisme, textes blancs
- Bouton CTA hero : vert `#22c55e`
- Cards formations : fond blanc, ombres légères
- Section formations : fond `#f8fafc`
- 3 nouvelles classes de section : `.cf2m-tools-section`, `.cf2m-why-section`, `.cf2m-cta-section`
- Collage hero : `.cf2m-hero-collage` avec 3 cartes superposées
- Footer : `.cf2m-footer-figma` avec colonnes structurées

### Templates
- Thème par défaut changé en `light`
- Navbar en sticky sur la home (plus en absolute)
- Footer : 4 colonnes (marque, formations, activités, informations)
- Hero : collage de 3 photos à la place du portrait circulaire
- Nouvelles sections home : Outils, Pourquoi CF2M, Entreprises & Recruteurs
