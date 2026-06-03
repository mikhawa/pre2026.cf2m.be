# 178 — Alias phpfix dans le Dockerfile PHP

**Date** : 2026-05-24  
**Modèle** : Haiku  
**Justification** : Modification simple d'une ligne dans un Dockerfile

## Fichiers modifiés
- `docker/php/Dockerfile` — ajout de l'alias `phpfix`

## Résumé
Ajout de l'alias shell `phpfix` dans le bloc des alias existants du Dockerfile PHP :

```dockerfile
&& echo "alias phpfix='./vendor/bin/php-cs-fixer fix'" >> /root/.shrc \
```

L'alias est disponible en `sh` (via `ENV=/root/.shrc`) et en `bash` (via `. /root/.shrc` dans `.bashrc`).

## Utilisation
Dans le conteneur PHP :
```bash
phpfix              # corrige tout le projet
phpfix src/         # corrige uniquement src/
```

## Résultat
✅ Alias ajouté — rebuild de l'image Docker nécessaire pour prendre effet (`docker compose build php`)
