# 182 — Documentation clé SSH de déploiement prod

**Modèle** : Sonnet
**Justification** : Documentation technique, aucune modification de code, analyse de workflow CI/CD existant.

## Fichiers modifiés / créés
- `docs/devops/ssh-key-deployment.md` ← nouveau fichier

## Résumé
Création d'un guide complet pour recréer la paire de clés SSH utilisée par GitHub Actions (`appleboy/ssh-action`) pour se connecter au VPS et déclencher le déploiement prod.

Couvre :
- Génération d'une clé ED25519 sans passphrase
- 3 méthodes pour déposer la clé publique sur le VPS (ssh-copy-id, manuel, Plesk)
- Mise à jour du secret `PROD_SSH_PRIVATE_KEY` dans GitHub
- Test de connexion avant déploiement
- Tableau de diagnostic des erreurs courantes
- Suppression propre d'une ancienne clé