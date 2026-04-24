---
modèle: haiku
date: 2026-04-24
justification: Ajout minimal dans deux fichiers existants, logique de session simple
---

# 156 — Mémorisation du chemin cible après double authentification

## Comportement
Avant la 2FA, l'URL demandée (requêtes GET uniquement) est sauvegardée en session sous la clé `2fa_target_path`.
Après validation du code, l'utilisateur est redirigé vers cette URL. Si aucune URL n'est mémorisée (ex. : connexion directe), retour au profil par défaut.

## Fichiers modifiés
- `src/EventSubscriber/TwoFactorKernelSubscriber.php` — sauvegarde de `getRequestUri()` en session avant la redirection vers la page 2FA
- `src/Controller/TwoFactorController.php` — lecture + suppression de `2fa_target_path` après validation, redirection vers l'URL mémorisée
