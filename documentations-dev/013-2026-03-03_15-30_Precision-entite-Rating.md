# 013 — Précision sur l'entité Rating

**Date** : 2026-03-03
**Fichier modifié** : `docs/architecture/database-schema.md`

## Changements
- Rating s'applique uniquement sur Works et Messages (pas sur les autres entités)
- Relations ManyToMany (et non ManyToOne nullable) :
  - `rating_works` : table de jointure Rating ↔ Works
  - `rating_messages` : table de jointure Rating ↔ Messages
- User reste en OneToMany → Rating (auteur de la note)
- Mise à jour des relations résumées
