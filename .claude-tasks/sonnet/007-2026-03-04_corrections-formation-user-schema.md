# 007 — Corrections Formation, User et database-schema

**Modèle** : Sonnet
**Justification** : Modifications multi-fichiers avec décisions d'architecture (relations ManyToOne, cohérence doc)
**Date** : 2026-03-04

## Fichiers modifiés

- `src/Entity/Formation.php` — ajout createdBy, updatedAt, updatedBy
- `src/Entity/User.php` — biography varchar 500 → 600
- `docs/architecture/database-schema.md` — corrections sémantiques et de mise en forme

## Résumé
Corrections suite à audit du schéma BDD :
- Formation : 3 champs manquants (createdBy ManyToOne obligatoire, updatedAt, updatedBy ManyToOne nullable)
- User.biography : longueur corrigée à 600
- Schema doc : "stagiaires" → "responsables", Messages → Comment, table jointure renommée, relations résumées unifiées

## Résultat
Entité Formation et documentation cohérentes.
