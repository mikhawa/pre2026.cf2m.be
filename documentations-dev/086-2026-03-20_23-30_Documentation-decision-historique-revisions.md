# 086 — Documentation décision architecturale — historique des révisions

**Date** : 2026-03-20 23:30

## Fichier créé
- `docs/architecture/decision-historique-revisions.md`

## Résumé
Documentation de la décision architecturale de remplacer la table `revision` polymorphique (JSON) par 3 tables typées (`formation_history`, `page_history`, `works_history`).

Contenu : contexte, schémas SQL des deux approches, pour/contre, comparaison des requêtes SQL, comparaison de la taille de base de données, conclusion et tableau des statuts.

## Raison
Demande de documentation pour conserver la justification du choix architectural à long terme.
