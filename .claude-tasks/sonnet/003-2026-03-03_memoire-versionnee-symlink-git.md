# Tâche : Versionnement de MEMORY.md dans git via symlink

**Numéro** : 003
**Date** : 2026-03-03
**Modèle utilisé** : Sonnet
**Justification du modèle** : Configuration système multi-fichiers, niveau intermédiaire.
**Complexité** : Moyenne
**Fichiers concernés** : `.claude/MEMORY.md` (nouveau), `CLAUDE.md`, symlink externe

## Objectif
Permettre le partage de la mémoire Claude entre plusieurs postes de travail via git.

## Ce qui a été fait
- Création de `.claude/MEMORY.md` dans le repo (contenu migré depuis l'externe)
- Remplacement du fichier externe par un symlink :
  `~/.claude/projects/-home-mikhawa-pre2026-cf2m-be/memory/MEMORY.md → .claude/MEMORY.md`
- `CLAUDE.md` : ajout de la procédure de setup symlink pour nouveaux postes
- `.claude/MEMORY.md` : ajout de la commande de setup dans le fichier lui-même

## Procédure nouveau poste
```bash
PROJECT_PATH="$(pwd)"  # depuis la racine du projet cloné
HASH=$(echo -n "$PROJECT_PATH" | sed 's|/|-|g')
mkdir -p ~/.claude/projects/${HASH}/memory
ln -sf "${PROJECT_PATH}/.claude/MEMORY.md" ~/.claude/projects/${HASH}/memory/MEMORY.md
```

## Résultat
- `git pull` → mémoire synchronisée sur tous les postes
- Claude Code charge automatiquement la mémoire via le symlink
- Aucune perte de contexte entre les postes de travail
