# 135 — En-tête, pied de page et hero en îlots React (maquette Figma)

**Date** : 2026-10-07 15:16
**Fichiers principaux** : `src/Service/Layout/LayoutPropsBuilder.php`, `src/Twig/LayoutExtension.php`,
`assets/react/src/islands/Layout/`, `assets/react/src/islands/Home/Hero.jsx`, `assets/styles/tokens.css`,
`assets/styles/layout.css`, `templates/base.html.twig`, `templates/home/index.html.twig`

## Résumé

L'en-tête, le pied de page et le hero de l'accueil sont désormais des îlots React. Le design de
l'en-tête et du hero suit la maquette Figma (en-tête transparent sur la photo, titre en capitales
espacées, chiffres clés cyan, bouton pilule, portrait dans l'anneau du logo). Le pied de page garde
le design actuel en attendant sa maquette. Les props sont construites côté serveur
(`LayoutPropsBuilder`), avec les liens filtrés selon les rôles.

## Raison

Début de l'intégration du nouveau design Figma, avec React (choix de l'équipe). Règles d'usage
(sécurité, Turbo, contenu de repli) : `docs/architecture/frontend.md`.
