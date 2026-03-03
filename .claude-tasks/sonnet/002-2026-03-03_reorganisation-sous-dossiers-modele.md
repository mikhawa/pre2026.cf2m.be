# Tâche : Réorganisation .claude-tasks/ en sous-dossiers par modèle

**Numéro** : 002
**Date** : 2026-03-03
**Modèle utilisé** : Sonnet
**Justification du modèle** : Modification de configuration documentaire — niveau intermédiaire.
**Complexité** : Simple
**Fichiers concernés** : `.claude-tasks/template.md`, `CLAUDE.md`, `memory/MEMORY.md`

## Objectif
Organiser les fichiers `.claude-tasks/` dans des sous-dossiers par modèle Claude utilisé.

## Ce qui a été fait
- Création de `.claude-tasks/sonnet/` avec déplacement du fichier 001
- `template.md` : ajout de la note de placement dans le bon sous-dossier
- `CLAUDE.md` : mise à jour du chemin avec les trois sous-dossiers
- `memory/MEMORY.md` : mise à jour de la convention

## Structure résultante
```
.claude-tasks/
├── template.md
├── haiku/     ← CRUD simple, migrations, typos
├── sonnet/    ← Controllers, services, tests, CI/CD
└── opus/      ← Architecture, sécurité, refactoring majeur
```

## Résultat
Convention opérationnelle. La numérotation NNN est globale (tous sous-dossiers confondus).
