# 004 — Création docs/architecture/database-schema.md

**Date** : 2026-03-03
**Fichier créé** : `docs/architecture/database-schema.md`

## Contenu
- Moteur : MariaDB 11.4, utf8mb4
- Schéma complet des 8 entités : User, Formation, Works, Messages, Inscription, ContactMessage, Page, Partenaire
- Champs, types et contraintes pour chaque entité
- Tableau des relations (ManyToOne, ManyToMany)
- Conventions de nommage BDD (snake_case, suffixe `_id`, tables de jointure)
