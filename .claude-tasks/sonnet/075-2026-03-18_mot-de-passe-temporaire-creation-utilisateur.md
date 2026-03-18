---
modèle: sonnet
justification: Controller EasyAdmin + envoi email + génération mot de passe — complexité métier moyenne
date: 2026-03-18
---

## Tâche 075 — Mot de passe temporaire à la création d'un utilisateur

### Problème
La création d'utilisateur via EasyAdmin échouait avec `SQLSTATE[23000]: Column 'password' cannot be null`
car aucun mot de passe n'était généré dans le formulaire.

### Solution
Surcharge de `persistEntity` dans `UserCrudController` :
1. Génération d'un mot de passe aléatoire de 12 caractères (lettres + chiffres + symboles `!@#$%&*`)
2. Hachage via `UserPasswordHasherInterface` et assignation à l'entité
3. Envoi d'un email de bienvenue à l'adresse du nouvel utilisateur avec les identifiants

### Fichiers modifiés
- `src/Controller/Admin/UserCrudController.php` — ajout constructeur + méthodes `persistEntity` et `generatePassword`
- `templates/emails/user_bienvenue.html.twig` — nouveau template email de bienvenue

### Résultat
La création d'utilisateur depuis EasyAdmin génère un mot de passe sécurisé, le persiste
et l'envoie à l'utilisateur par email. L'utilisateur est invité à le changer depuis son profil.
