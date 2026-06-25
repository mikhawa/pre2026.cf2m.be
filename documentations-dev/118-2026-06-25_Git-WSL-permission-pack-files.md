---
date: 2026-06-25
fichiers modifiés:
  - .git/config (git config local — non versionné)
  - docs/devops/git-wsl-pack-permissions.md (créé)
---

## Résumé

Correction du problème de permissions sur les fichiers pack git sous WSL2.

## Symptôme

`git pull` / `git push` échouaient avec :
```
fatal: could not write multi-pack-index: Permission denied
error: task 'geometric-repack' failed
```

Windows Defender verrouillait les fichiers `.pack`/`.idx` dans le répertoire WSL2,
empêchant git de les supprimer lors du repack géométrique automatique.

## Actions

1. Suppression du `multi-pack-index` corrompu : `rm .git/objects/pack/multi-pack-index`
2. Désactivation du repack géométrique automatique :
   ```bash
   git config maintenance.incremental-repack.enabled false
   git config gc.auto 0
   ```
3. Repack manuel pour consolider les 20+ fichiers pack accumulés :
   ```bash
   git repack -a -d
   ```
   Résultat : 20+ packs → 5 packs.

## Raison

Accumulation de fichiers pack sans nettoyage automatique (bloqué par Windows Defender
sur le système de fichiers WSL2).

## Solution permanente à appliquer

Exclure le répertoire de Windows Defender (PowerShell admin Windows) :
```powershell
Add-MpPreference -ExclusionPath "\\wsl.localhost\Ubuntu\home\mikhawa\pre2026_cf2m_be"
```

Voir `docs/devops/git-wsl-pack-permissions.md` pour la procédure complète.
