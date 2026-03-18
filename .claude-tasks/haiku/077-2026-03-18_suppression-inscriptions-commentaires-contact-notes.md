---
modèle: haiku
justification: Configuration EasyAdmin simple — ajout/retrait de permissions DELETE
date: 2026-03-18
---

## Tâche 077 — Règles de suppression EasyAdmin (inscriptions, commentaires, contact, notes)

### Règles appliquées
- **Inscription** : DELETE désactivé pour tous (piste d'audit)
- **Commentaire** : DELETE désactivé pour tous + configureActions ajouté
- **ContactMessage** : DELETE désactivé pour tous (piste d'audit)
- **Rating (Note)** : DELETE réservé à ROLE_SUPER_ADMIN uniquement

### Fichiers modifiés
- `src/Controller/Admin/InscriptionCrudController.php` — DELETE retiré de setPermission, ajouté à disable()
- `src/Controller/Admin/CommentCrudController.php` — configureActions ajouté avec DELETE désactivé
- `src/Controller/Admin/ContactMessageCrudController.php` — DELETE ajouté à disable()
- `src/Controller/Admin/RatingCrudController.php` — DELETE retiré de disable(), setPermission ROLE_SUPER_ADMIN
