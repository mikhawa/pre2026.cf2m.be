---
modèle: haiku
date: 2026-03-28
---

# 120 — Correction PATH PHP dans le workflow de déploiement SSH

## Justification du modèle
Modification simple d'un fichier de configuration CI/CD — Haiku suffit.

## Fichiers modifiés
- `.github/workflows/deploy-prod.yml`

## Résumé
Ajout de `export PATH` au début du script SSH pour que `php` et `composer` soient trouvables lors du déploiement via GitHub Actions.

Le VPS utilise `phpenv` : le binaire PHP est dans `/var/www/vhosts/cf2m.be/.phpenv/shims/php`.
Les sessions SSH non-interactives ne chargent pas le profil utilisateur, donc le PATH n'incluait pas phpenv.

## Erreur corrigée
```
/usr/bin/env: 'php': No such file or directory
Process exited with status 127
```

## Solution appliquée
```yaml
export PATH="/var/www/vhosts/cf2m.be/.phpenv/shims:/var/www/vhosts/cf2m.be/.phpenv/bin:$PATH"
```
