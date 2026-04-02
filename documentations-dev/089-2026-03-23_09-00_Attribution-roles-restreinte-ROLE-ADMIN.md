# 089 — Attribution de rôles restreinte pour ROLE_ADMIN

**Date** : 2026-03-23 09:00
**Branche** : main

## Fichiers modifiés
- `src/Controller/Admin/UserCrudController.php`

## Changements

### Champ `roles` dans le formulaire utilisateur
Avant : visible uniquement par `ROLE_SUPER_ADMIN` (`->setPermission('ROLE_SUPER_ADMIN')`)
Après : visible par `ROLE_ADMIN` et `ROLE_SUPER_ADMIN`, avec des choix différents selon le rôle connecté.

**En affichage (index)** : tous les rôles sont déclarés dans les choix pour que les badges s'affichent correctement (`ROLE_USER`, `ROLE_STAGIAIRE`, `ROLE_FORMATEUR`, `ROLE_ADMIN`, `ROLE_SUPER_ADMIN`).

**En formulaire (new/edit) pour ROLE_ADMIN** : choix limités à `ROLE_ADMIN` et `ROLE_FORMATEUR` uniquement.

**En formulaire pour ROLE_SUPER_ADMIN** : tous les choix disponibles (comportement inchangé).

### Garde-fou serveur (`updateEntity`)
Nouvelle méthode `updateEntity()` : si l'utilisateur connecté n'est pas `ROLE_SUPER_ADMIN`, `ROLE_SUPER_ADMIN` est retiré des rôles avant persistance, même en cas de manipulation directe de la requête HTTP.

### Masquage du bouton Modifier pour les ROLE_SUPER_ADMIN
`configureActions()` : `->displayIf()` sur l'action `EDIT` en index — les boutons sont masqués pour les utilisateurs `ROLE_SUPER_ADMIN` lorsque l'utilisateur connecté est seulement `ROLE_ADMIN`. Plus de message d'erreur après clic, le bouton n'apparaît pas.

### Imports nettoyés
Suppression des imports devenus inutiles : `AdminContext`, `KeyValueStore`, `Response`, `ArrayField`, `BooleanField`.

## Raison
Un `ROLE_ADMIN` doit pouvoir gérer les rôles des utilisateurs mais sans pouvoir élever quelqu'un au rang de Super Administrateur, ni modifier les Super Administrateurs existants.
