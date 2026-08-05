---
name: bug-profil-public-accessible
description: La route app_public_profile (/utilisateur/{id}) expose les profils utilisateurs sans authentification — signalé par l'utilisateur comme ne devant pas être public
metadata:
  type: project
---

La route `app_public_profile` (`/utilisateur/{id}`, `templates/profil/public.html.twig`) est accessible sans authentification et affiche des informations de profil utilisateur (nom, avatar, etc. — voir le template pour le détail exact des champs exposés).

**Signalé par l'utilisateur (2026-08-05)** : "les utilisateurs ne peuvent pas être publics" — cette route ne devrait probablement pas exister sous cette forme, ou devrait être restreinte (auth requise, ou opt-in explicite de l'utilisateur concerné, ou restriction des champs affichés).

**Contexte de la découverte** : repéré incidemment lors de la vérification visuelle de l'étape 7 du restyle "raffiné white/dark" (voir [[restyle-white-dark]]), en cherchant une page de profil à capturer sans avoir de session authentifiée.

**How to apply** : ne pas corriger dans le cadre du restyle CSS (hors scope, pas demandé). À traiter comme une tâche de sécurité/vie privée à part entière — vérifier avec l'utilisateur quels champs doivent rester visibles publiquement (s'il en reste) avant de modifier `ProfilController` / la route / le voter d'accès.
