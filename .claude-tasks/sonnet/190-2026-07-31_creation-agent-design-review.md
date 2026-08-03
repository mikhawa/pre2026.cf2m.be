# Tâche : Création de l'agent design-review

**Numéro** : 190
**Date** : 2026-07-31
**Modèle utilisé** : Sonnet
**Justification du modèle** : Rédaction d'un sous-agent (prompt système + configuration) nécessitant de connaître la stack front (Twig, Bootstrap 5.3, Stimulus/Turbo, EasyAdmin) et de formuler une méthode de revue cohérente — au-delà d'un CRUD simple, mais sans enjeu d'architecture ou de sécurité justifiant Opus.
**Complexité** : Simple
**Fichiers concernés** : `.claude/agents/design-review.md`

## Contexte nécessaire
Le projet n'avait pas encore de dossier `.claude/agents/`. L'utilisateur a demandé la création d'un "agent de design". Clarification obtenue via question : un agent de revue UI/UX du site existant (pas de génération Figma → code, ni d'architecture technique).

## Objectif
Créer un sous-agent `design-review` capable de revoir les templates Twig, le CSS et les contrôleurs Stimulus du site pour la cohérence visuelle, l'accessibilité et le responsive, en s'appuyant sur les conventions déjà en place (Bootstrap 5.3.8, ImportMap, EasyAdmin pour le back-office).

## Contraintes
- Rédaction entièrement en français (langue du projet)
- Agent de revue uniquement (pas d'édition automatique du code)
- Outils limités à Read, Grep, Glob, Bash (lecture/inspection uniquement)

## Critères d'acceptation
- [x] Frontmatter avec `name`, `description` (avec exemples d'usage), `tools`, `model`
- [x] Méthode de revue adaptée à la stack réelle du projet (vérifiée via inspection de `templates/`, `assets/`, `importmap.php`)
- [x] Format de sortie structuré en français

## Résultat
Fichier `.claude/agents/design-review.md` créé. L'agent compare les pages/composants modifiés à des pages similaires existantes, vérifie cohérence Bootstrap, accessibilité, responsive, et compatibilité Stimulus/Turbo, et peut s'appuyer sur le MCP Figma si une maquette est fournie. Sortie structurée : résumé, problèmes (fichier:ligne), points positifs.
