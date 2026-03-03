# 002 — Ajout des TODO de mise en production dans le workflow

**Date** : 2026-03-03
**Fichier modifié** : `.github/workflows/symfony.yml`

## Changements
Ajout d'un bloc de commentaires `# TODO (mise en production)` en haut du fichier listant les points à traiter avant le déploiement en production :
- Adapter les branches déclenchant le workflow
- Remplacer SQLite par la vraie base de données
- Configurer les secrets GitHub
- Ajouter une étape de déploiement
- Séparer les workflows de tests et de déploiement
- Vérifier la version PHP du serveur cible
