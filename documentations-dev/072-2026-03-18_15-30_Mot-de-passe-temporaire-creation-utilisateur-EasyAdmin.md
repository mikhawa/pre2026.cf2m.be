# 072 — Mot de passe temporaire à la création d'un utilisateur (EasyAdmin)

**Date** : 2026-03-18 15:30
**Branche** : `feature/add-icones-into-admin`

## Problème
Lors de la création d'un utilisateur via EasyAdmin, l'erreur suivante survenait :
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'password' cannot be null
```
Le formulaire de création ne proposait pas de champ mot de passe, et aucun mot de passe
n'était généré automatiquement.

## Solution
Surcharge de `persistEntity` dans `UserCrudController` :
- Génération d'un mot de passe aléatoire de **12 caractères** (majuscules, minuscules, chiffres, symboles)
- Hachage via `UserPasswordHasherInterface`
- Envoi d'un email de bienvenue au nouvel utilisateur avec ses identifiants
- L'utilisateur est invité à modifier son mot de passe depuis son profil

## Fichiers modifiés
| Fichier | Action |
|---|---|
| `src/Controller/Admin/UserCrudController.php` | Ajout constructeur + `persistEntity` + `generatePassword` |
| `templates/emails/user_bienvenue.html.twig` | Nouveau template email de bienvenue |

## Comportement
1. Super admin remplit le formulaire (email, nom d'utilisateur, rôles)
2. À la validation, un mot de passe de 12 caractères est généré et haché
3. Un email est envoyé au nouvel utilisateur avec ses identifiants
4. L'utilisateur se connecte et modifie son mot de passe depuis `/profil/edit`
