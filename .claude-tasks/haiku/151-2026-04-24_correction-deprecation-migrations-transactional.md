---
modèle: haiku
date: 2026-04-24
justification: Correction de configuration simple, une ligne dans un fichier YAML
---

# 151 — Correction dépréciation Doctrine Migrations : transactional

## Problème
Notice de dépréciation en production lors de la migration `Version20260424044040` :
> the transaction is already committed, relying on silencing is deprecated.

Cause : MariaDB provoque un commit implicite sur tout DDL (`ALTER TABLE`, `CREATE TABLE`, etc.), ce qui entre en conflit avec le wrapping transactionnel de Doctrine Migrations.

## Solution
Ajout de `transactional: false` dans la configuration globale Doctrine Migrations.

## Fichiers modifiés
- `config/packages/doctrine_migrations.yaml`
