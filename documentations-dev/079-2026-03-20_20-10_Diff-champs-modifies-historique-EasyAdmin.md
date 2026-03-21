# 079 — Affichage des champs modifiés dans l'historique EasyAdmin

**Date** : 2026-03-20 20:10
**Tâche** : 089

## Fichier modifié
- `src/Service/RevisionService.php` — méthode `buildTypedHistoryDiffHtml()`

## Résumé
Amélioration de l'affichage du diff dans les pages d'historique (Formation, Page, Works) :

**Création initiale (`$before === null`)** :
- Avant : seul un badge « Création initiale » sans aucune valeur
- Après : badge + liste de tous les champs non vides en vert (valeurs initiales), avec bouton collapse pour les champs riches (description, content)

**Modification (`$before !== null`)** :
- Ajout d'un résumé « N champ(s) modifié(s) » en en-tête du diff
- Le reste est inchangé : seuls les champs dont la valeur a changé sont affichés (ancienne valeur en rouge → nouvelle en vert)

## Raison
Demande utilisateur : rendre visible le détail des champs dans chaque entrée d'historique EasyAdmin.
