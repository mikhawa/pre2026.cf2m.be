---
modèle: haiku
justification: Diagnostic et configuration git simple, aucune modification de code applicatif
fichiers modifiés:
  - .git/config (local)
  - docs/devops/git-wsl-pack-permissions.md (créé)
  - documentations-dev/118-2026-06-25_Git-WSL-permission-pack-files.md (créé)
---

## Tâche

Correction du problème `Permission denied` sur le repack géométrique git sous WSL2.

## Diagnostic

- Windows Defender verrouille les fichiers `.pack`/`.idx` dans le filesystem WSL2
- 20+ fichiers pack accumulés → git déclenche un repack géométrique automatique
- Le repack échoue car il ne peut pas supprimer les anciens packs verrouillés

## Actions

```bash
rm .git/objects/pack/multi-pack-index
git gc --auto
git config maintenance.incremental-repack.enabled false
git config gc.auto 0
git repack -a -d
```

## Résultat

20+ fichiers pack → 5 fichiers. Plus d'erreur `Permission denied` au pull/push.

## À faire (utilisateur)

Ajouter l'exclusion Windows Defender pour résoudre définitivement le problème :
```powershell
Add-MpPreference -ExclusionPath "\\wsl.localhost\Ubuntu\home\mikhawa\pre2026_cf2m_be"
```
