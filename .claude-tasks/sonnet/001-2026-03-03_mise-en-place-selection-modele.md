# Tâche : Mise en place du système de sélection de modèle et traçabilité

**Numéro** : 001
**Date** : 2026-03-03
**Modèle utilisé** : Sonnet
**Justification du modèle** : Tâche de configuration documentaire sans logique métier complexe — niveau intermédiaire (modification de plusieurs fichiers de configuration/mémoire).
**Complexité** : Moyenne
**Fichiers concernés** : `CLAUDE.md`, `.claude-tasks/template.md`, `memory/MEMORY.md`

## Objectif
Mettre en place le lien entre `.claude/models.md` (règles de sélection) et `.claude-tasks/` (traçabilité du modèle utilisé).

## Ce qui a été fait
- `CLAUDE.md` : ajout d'une section détaillée sur la sélection obligatoire du modèle et la traçabilité `.claude-tasks/`
- `.claude-tasks/template.md` : refonte du template avec champs modèle, justification, numéro, résultat
- `memory/MEMORY.md` : ajout des conventions `.claude-tasks/` et sélection du modèle pour persistance inter-sessions

## Résultat
Système opérationnel. À chaque tâche modifiant du code, Claude doit :
1. Évaluer la complexité → choisir Haiku/Sonnet/Opus
2. Déléguer via `Agent` si le modèle optimal diffère du modèle courant
3. Créer un fichier `NNN-YYYY-MM-DD_description.md` dans `.claude-tasks/{modèle}/`
