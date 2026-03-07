# Mémoire du projet CF2m

## Convention de changelog obligatoire
À chaque changement effectué, créer un fichier dans `documentations-dev/` :
- Format : `NNN-YYYY-MM-DD_HH-MM_Description-courte.md`
- Numérotation auto-incrémentée (3 chiffres)
- Vérifier le dernier numéro existant avant de créer un nouveau fichier
- Contenu minimal : date, fichier(s) modifié(s), résumé des changements, raison

## Stack projet
- Symfony 7.4 LTS | PHP 8.5 | MariaDB 11.4 | Docker | ImportMap
- Back-office : EasyAdmin 4
- Éditeur riche : SunEditor (via Stimulus controller + ImportMap)
- Uploads : vich/uploader-bundle + ImageResizeService
- Frontend : Bootstrap 5 + Stimulus + Turbo (pas de bundler)

## Fichiers clés
- `CLAUDE.md` — règles générales du projet
- `.claude/CONTEXT.md` — entités et services détaillés
- `.claude/models.md` — règles d'attribution des modèles
- `docs/architecture/overview.md` — architecture globale
- `docs/architecture/database-schema.md` — schéma BDD
- `docs/architecture/easyadmin.md` — back-office EasyAdmin
- `docs/architecture/frontend.md` — design et frontend
- `docs/devops/docker-setup.md` — Docker dev
- `docs/devops/vps-preprod.md` — déploiement VPS
- `docs/devops/github-actions.md` — CI/CD

## Conventions entités (voir User.php comme référence)
- `declare(strict_types=1)` obligatoire
- Attributs PHP 8 Doctrine (`#[ORM\*]`)
- `#[ORM\PrePersist]` pour `createdAt`, `updatedAt` mis à jour dans le setter VichUploader
- `#[Vich\Uploadable]` + `#[Vich\UploadableField]` pour les fichiers (y compris User.avatarFile)
- `#[Assert\*]` pour toute validation
- `plainPassword` non mappé, effacé via `eraseCredentials()`
- Setters retournent `static`, `__toString()` retourne le champ principal
- `id` toujours `unsigned`, `status` en `smallint unsigned`
- `orphanRemoval: true` sur les collections OneToMany
- PHPDoc : `/** @var list<string> */` et `/** @var Collection<int, Entity> */`

## Entités à créer (référencées mais manquantes)
- `Comment` (référencée dans User.php)
- `Rating` (référencée dans User.php)

## Convention .claude-tasks/ obligatoire
Pour **toute tâche modifiant du code**, créer un fichier dans le sous-dossier correspondant au modèle :
- Chemins : `.claude-tasks/haiku/`, `.claude-tasks/sonnet/`, `.claude-tasks/opus/`
- Format : `NNN-YYYY-MM-DD_description-courte.md`
- Numérotation globale (tous sous-dossiers confondus), indépendante de `documentations-dev/`
- Contenu minimal : modèle utilisé, justification, fichiers modifiés, résumé, résultat
- Vérifier le dernier numéro dans TOUS les sous-dossiers de `.claude-tasks/` avant de créer

## Sélection du modèle (avant chaque tâche)
Lire `.claude/models.md` et choisir :
- **Haiku** → CRUD simple, migrations, typos, syntaxe
- **Sonnet** → Controllers, services, tests, CI/CD
- **Opus** → Architecture, sécurité, refactoring majeur, bugs complexes
Pour déléguer : outil `Agent` avec `model: "opus"` ou `model: "haiku"`

## Mémoire versionnable (symlink git)
Ce fichier (`.claude/MEMORY.md`) est versionné dans le repo git.
Sur chaque nouveau poste, créer le symlink après le clone :
```bash
PROJECT_PATH="$(pwd)"  # depuis la racine du projet cloné
HASH=$(echo -n "$PROJECT_PATH" | sed 's|/|-|g')
mkdir -p ~/.claude/projects/${HASH}/memory
ln -sf "${PROJECT_PATH}/.claude/MEMORY.md" ~/.claude/projects/${HASH}/memory/MEMORY.md
```

## Dernier numéro de changelog
033 (2026-03-07)

## Dernier numéro .claude-tasks
024 (2026-03-07)

## Convention navbar (couleur uniforme)
Sur les pages intérieures (non-home, non-login), la navbar doit avoir `background: var(--cf2m-dark)` + `backdrop-filter: none` pour rester visuellement identique à son apparence sur la home (qui flotte sur fond sombre). Ne jamais laisser le `backdrop-filter` actif sur fond blanc.

## Conventions Foundry 2.x (factories)
- `createMany()` / `createOne()` retournent des entités directement (pas de proxies, pas de `_real()`)
- Pour les booleans `is*` : utiliser la clé sans préfixe `is` pour PropertyAccessor
  - `isActive` → clé `'active'` (setter `setActive()`)
  - `isApproved` → clé `'approved'` (setter `setApproved()`)
  - `isRead` → clé `'read'` (setter `setRead()`)
- Defaults callable (`fn():array`) pour générer slug depuis le titre
- Hachage MDP dans `afterInstantiate` via injection de `UserPasswordHasherInterface`
