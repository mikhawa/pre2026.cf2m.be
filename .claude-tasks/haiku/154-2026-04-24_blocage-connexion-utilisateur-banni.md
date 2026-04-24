---
modèle: haiku
date: 2026-04-24
justification: Ajout d'un cas dans le UserChecker existant, modification minimale
---

# 154 — Blocage de connexion pour les utilisateurs bannis (status = 2)

## Comportement
Un utilisateur avec status = 2 (Banni) ne peut plus se connecter.
Message affiché : "Votre compte a été suspendu. Contactez l'administration pour plus d'informations."
Son contenu (works, commentaires, etc.) reste intact.
Seul le super admin peut supprimer du contenu au cas par cas depuis EasyAdmin.

## Fichiers modifiés
- `src/Security/UserChecker.php` — ajout du cas status === 2 dans checkPreAuth()
