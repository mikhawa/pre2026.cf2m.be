# Mémoire du projet CF2m

Index des mémoires — le détail de chaque sujet est dans son propre fichier (`[[nom]]` = fichier lié dans ce dossier).

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

## Mémoire versionnable (symlink git)
Ce dossier (`.claude/memory/`) est versionné dans le repo git — index (`MEMORY.md`) + fichiers thématiques.
Sur chaque nouveau poste, créer le symlink après le clone :
```bash
PROJECT_PATH="$(pwd)"  # depuis la racine du projet cloné
HASH=$(echo -n "$PROJECT_PATH" | sed 's|/|-|g')
mkdir -p ~/.claude/projects/${HASH}
rm -rf ~/.claude/projects/${HASH}/memory
ln -sf "${PROJECT_PATH}/.claude/memory" ~/.claude/projects/${HASH}/memory
```

## Dernier numéro de changelog
129 (2026-08-05) — Vérification finale du restyle "raffiné white/dark" : plan terminé

## Dernier numéro .claude-tasks
197 (2026-08-05)

## Règle absolue : aucune initiative
Ne jamais modifier un fichier qui n'a pas été explicitement demandé. Toujours demander avant d'étendre une modification à d'autres fichiers.

## Index des sujets détaillés
- [Dark/Light mode](dark-light-mode.md) — TERMINÉ ✅ — toggle CSS-driven, anti-flash, palette light mode d'origine
- [Restyle visuel "raffiné white/dark"](restyle-white-dark.md) — TERMINÉ ✅ (2026-08-05) — refonte en 7 étapes à partir d'un mockup, méthode de vérification par capture d'écran
- [Bug : profil public accessible](bug-profil-public-accessible.md) — `/utilisateur/{id}` sans auth, signalé par l'utilisateur, à corriger dans un futur chantier
- [Historique typées (revision → 3 tables)](historique-typees.md) — TERMINÉE ✅ — refactorisation en 5 phases
- [ROLE_PEDAGO](role-pedago.md) — TERMINÉ ✅ (2026-04-04) — hiérarchie et permissions
- [Mailer](mailer.md) — Mailpit (dev) vs Mailjet (préprod/prod)
- [Convention navbar](navbar-convention.md) — fond toujours sombre, même en light mode
- [Convention CSRF](csrf-convention.md) — mécanisme stateless, import JS obligatoire
- [Conventions entités](conventions-entites.md) — voir User.php comme référence + entités manquantes
- [Conventions Foundry 2.x](foundry-conventions.md) — factories, PropertyAccessor sur les booleans
- [EasyAdmin 4 — JS/DOM](easyadmin-js-dom.md) — sélecteurs de menu, CSS AssetMapper, event delegation Turbo
