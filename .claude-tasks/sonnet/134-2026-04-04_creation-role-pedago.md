---
modèle: Sonnet
justification: Nouveau rôle métier, voters, permissions EasyAdmin, emails — impacte 12 fichiers
date: 2026-04-04
---

# Tâche 134 — Création du rôle ROLE_PEDAGO

## Fichier créé

- `src/Security/Voter/ContentManagerVoter.php` — attribut `CONTENT_MANAGER` (ROLE_ADMIN || ROLE_PEDAGO), utilisé dans setPermission() et denyAccessUnlessGranted()

## Fichiers modifiés

- `config/packages/security.yaml` — `ROLE_PEDAGO: [ROLE_FORMATEUR, ROLE_STAGIAIRE, ROLE_USER]`
- `src/Security/Voter/FormationVoter.php` — ROLE_PEDAGO = auto-approve comme ROLE_ADMIN ; ajout attribut `FORMATION_CREATE` (sans sujet)
- `src/Controller/Admin/FormationCrudController.php` — NEW via `FORMATION_CREATE` ; filtre index exclut PEDAGO (voit tout)
- `src/Controller/Admin/WorksCrudController.php` — PEDAGO lecture seule : disable NEW/EDIT en configureActions, deny dans edit() et updateEntity() ; filtre index PEDAGO voit tout
- `src/Controller/Admin/PageCrudController.php` — toutes les permissions ROLE_ADMIN → CONTENT_MANAGER
- `src/Controller/Admin/InscriptionCrudController.php` — ROLE_ADMIN → CONTENT_MANAGER
- `src/Controller/Admin/UserCrudController.php` — ROLE_ADMIN → CONTENT_MANAGER ; ajout ROLE_PEDAGO dans les choix (badge primary)
- `src/Controller/Admin/ContactMessageCrudController.php` — ajout permissions CONTENT_MANAGER
- `src/Controller/Admin/DashboardController.php` — menu Pages/Utilisateurs/Inscriptions/Contact → CONTENT_MANAGER
- `src/Repository/UserRepository.php` — ajout `findInscriptionRecipients()` et `findContactRecipients()` (ROLE_ADMIN + ROLE_SUPER_ADMIN + ROLE_PEDAGO)
- `src/Controller/InscriptionController.php` — findAdmins() → findInscriptionRecipients()
- `src/Controller/ContactController.php` — envoi copie aux ROLE_PEDAGO (en plus de MAIL_ADMIN fixe)

## Résumé des droits ROLE_PEDAGO

| Ressource | Droits |
|---|---|
| Formations | Créer, modifier (AUTO_APPROVED), voir tout, historique |
| Works | Lecture seule (INDEX + DETAIL uniquement) |
| Pages | Accès complet (comme ROLE_ADMIN) |
| Inscriptions | Gérer (INDEX, EDIT, DETAIL) |
| Utilisateurs | Créer et gérer (INDEX, NEW, EDIT, DETAIL) |
| Messages de contact | Voir et marquer comme lu |
| Révisions en attente | Non visible dans le menu |
| Mail préinscription | Reçoit (findInscriptionRecipients) |
| Mail contact | Reçoit (findContactRecipients, en copie de MAIL_ADMIN) |
| Mail révision | Ne reçoit pas |
