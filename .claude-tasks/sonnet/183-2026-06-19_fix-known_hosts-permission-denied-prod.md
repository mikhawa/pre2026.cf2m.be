# 183 — Fix : known_hosts Permission denied sur déploiement prod

**Modèle** : Sonnet
**Justification** : Diagnostic CI/CD, modification workflow GitHub Actions.

## Fichiers modifiés
- `.github/workflows/deploy-prod.yml`

## Symptôme
```
hostkeys_find_by_key_hostfile: hostkeys_foreach failed for
/var/www/vhosts/cf2m.be/helpdesk.cf2m.be/.ssh/known_hosts: Permission denied
Host key verification failed.
fatal: Could not read from remote repository.
```

## Cause
L'utilisateur SSH de déploiement a son HOME dans `/var/www/vhosts/cf2m.be/helpdesk.cf2m.be/`.
Lors du `git pull`, SSH tente de lire `~/.ssh/known_hosts` pour vérifier la clé hôte de GitHub,
mais le fichier (ou le dossier `.ssh`) a des permissions incorrectes → Permission denied.

La préprod fonctionnait car son chemin de déploiement est `pre2026.cf2m.be` (HOME différent avec known_hosts lisible).

## Fix appliqué
Ajout de `GIT_SSH_COMMAND` avant le `git pull` dans `deploy-prod.yml` :
```bash
export GIT_SSH_COMMAND="ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null"
```
Cela court-circuite complètement le known_hosts problématique.

## Fix complémentaire recommandé (sur le VPS)
Corriger les permissions à la main pour éviter le problème à la racine :
```bash
chmod 700 /var/www/vhosts/cf2m.be/helpdesk.cf2m.be/.ssh
chmod 600 /var/www/vhosts/cf2m.be/helpdesk.cf2m.be/.ssh/known_hosts
```