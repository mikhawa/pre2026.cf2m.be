# Git sous WSL2 — Problèmes de permissions sur les fichiers pack

## Symptôme

Lors d'un `git pull` ou `git push`, git tente un **repack géométrique** automatique
(consolidation des fichiers `.pack`/`.idx`) et échoue avec :

```
fatal: could not write multi-pack-index: Permission denied
error: failed to perform geometric repack
error: task 'geometric-repack' failed
```

Ou en mode interactif :

```
Unlink of file '.git/objects/pack/pack-XXXX.idx' failed. Should I try again? (y/n)
```

## Cause

Windows Defender (ou tout autre processus Windows : VS Code Windows, indexeur de
recherche, antivirus tiers) verrouille les fichiers dans le système de fichiers WSL2,
empêchant git de supprimer les anciens packs lors de la réorganisation.

Le problème s'aggrave avec le temps : chaque `git pull` ajoute un fichier pack sans
jamais en supprimer, jusqu'à atteindre 20+ fichiers et déclencher le repack automatique.

## Correction appliquée (2026-06-25)

### 1. Désactivation du repack géométrique automatique

```bash
git config maintenance.incremental-repack.enabled false
git config gc.auto 0
```

Ces options sont stockées dans `.git/config` (local au dépôt, non versionné).

### 2. Nettoyage manuel des fichiers pack accumulés

```bash
git repack -a -d
```

Résultat : 20+ fichiers pack → 5 fichiers. Aucune perte de données.

## Solution permanente recommandée

Exclure le répertoire WSL2 de Windows Defender depuis **PowerShell Windows (administrateur)** :

```powershell
Add-MpPreference -ExclusionPath "\\wsl.localhost\Ubuntu\home\mikhawa\pre2026_cf2m_be"
```

Vérification de l'exclusion :

```powershell
Get-MpPreference | Select-Object -ExpandProperty ExclusionPath
```

Une fois l'exclusion Windows Defender en place, on peut réactiver le gc automatique :

```bash
git config --unset maintenance.incremental-repack.enabled
git config --unset gc.auto
```

## Prévention

Si le symptôme réapparaît après plusieurs mois, relancer simplement :

```bash
git repack -a -d
```

Cela consolide tous les packs sans déclencheur automatique.