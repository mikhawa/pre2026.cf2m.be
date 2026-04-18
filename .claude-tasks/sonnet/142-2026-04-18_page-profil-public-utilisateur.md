---
modèle: sonnet
justification: Création d'un controller et d'un template avec logique de rôle prioritaire
fichiers modifiés: []
fichiers créés:
  - src/Controller/PublicProfileController.php
  - templates/profil/public.html.twig
---

## Résumé

Nouvelle page de profil public accessible sans authentification à `/utilisateur/{id}`.

## Fonctionnalités

- Affiche : nom (`userName`), avatar (ou placeholder SVG), rôle principal (badge coloré), date d'inscription, biographie, liens externes
- Rôle affiché : le plus élevé dans la hiérarchie SUPER_ADMIN → ADMIN → PEDAGO → FORMATEUR → STAGIAIRE → USER
- Aucune information sensible (pas d'email)
- Route `app_public_profile` générée par `EntityValueResolver` Symfony (404 automatique si id inconnu)
- Fil d'ariane : Accueil → nom de l'utilisateur
