# Projet : Site du Centre de Formation CF2m

**Objectif** : Plateforme de gestion des formations, inscriptions, et ressources pour les étudiants et formateurs du CF2m.

**Public cible** : Étudiants, formateurs, pouvoirs subsidiants, entreprises et administrateurs du CF2m.

**Langue du projet** : Ce projet se fait entièrement en français : code (commentaires, messages de validation, noms de commits), documentation, et échanges.

**Stack** : Symfony 7.4 LTS --webapp | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap.

**Environnements** : dev (Docker local) → préprod (GitHub CI) → prod (VPS Debian 12.13 sous Plesk).

**Repo** : https://github.com/mikhawa/pre2026.cf2m.be

## Mémoire persistante
`.claude/MEMORY.md` est versionné dans git et chargé automatiquement via symlink.
Sur un nouveau poste après `git clone`, exécuter :
```bash
PROJECT_PATH="$(pwd)"
HASH=$(echo -n "$PROJECT_PATH" | sed 's|/|-|g')
mkdir -p ~/.claude/projects/${HASH}/memory
ln -sf "${PROJECT_PATH}/.claude/MEMORY.md" ~/.claude/projects/${HASH}/memory/MEMORY.md
```

## Règles d'utilisation des modèles Claude
Voir `.claude/models.md` pour les critères détaillés.

### Sélection obligatoire avant chaque tâche
Évaluer la complexité et choisir le modèle adapté :
- **Haiku** → CRUD simple, migrations, typos, questions syntaxe
- **Sonnet** → Controllers métier, services, tests, CI/CD
- **Opus** → Architecture, sécurité, refactoring majeur, bugs complexes

Pour déléguer à un modèle supérieur/inférieur, utiliser l'outil `Agent` avec le paramètre `model: "opus"` ou `model: "haiku"`.

### Traçabilité obligatoire (.claude-tasks/)
Pour **toute tâche modifiant du code**, créer un fichier dans `.claude-tasks/` :
- **Chemin** : `.claude-tasks/haiku/`, `.claude-tasks/sonnet/` ou `.claude-tasks/opus/` selon le modèle
- **Format** : `NNN-YYYY-MM-DD_description-courte.md`
- **Numérotation** : auto-incrémentée sur 3 chiffres, globale (tous sous-dossiers confondus), indépendante de `documentations-dev/`
- **Contenu minimal** : modèle utilisé, justification, fichiers modifiés, résumé
- Vérifier le dernier numéro existant dans tous les sous-dossiers de `.claude-tasks/` avant de créer un fichier

## Contexte rapide
- Architecture : MVC + couche Service + Repository pattern
- Auth : Symfony Security (voters custom)
- Frontend : ImportMap + Stimulus + Turbo (pas de build step)
- Déploiement : GitHub Actions → SSH VPS

## Fichiers clés à connaître
- Architecture globale : `docs/architecture/overview.md`
- Docker dev : `docs/devops/docker-setup.md`
- Schéma BDD : `docs/architecture/database-schema.md`
- Déploiement : `docs/devops/vps-preprod.md`

## Conventions de code
- Entités : `src/Entity/` — annotations Doctrine en attributs PHP 8
- Services métier : `src/Service/`
- Repositories custom : `src/Repository/`
- Nommage : PascalCase classes, snake_case BDD, camelCase JS
