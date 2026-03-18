# 068 — Boutons save colorés + compteur Historique

**Modèle** : Sonnet
**Justification** : Configuration EasyAdmin (controllers + repository)

## Fichiers modifiés
- `src/Repository/RevisionRepository.php` — ajout `countByEntityId(string $type, int $id): int`
- `src/Controller/Admin/FormationCrudController.php` — injection `RevisionRepository`, label callable Historique (N), boutons save bleu/vert
- `src/Controller/Admin/PageCrudController.php` — boutons save bleu/vert
- `src/Controller/Admin/WorksCrudController.php` — ajout `configureActions()`, boutons save bleu/vert

## Résumé
- Bouton "Historique" sur Formation affiche le nombre de révisions entre parenthèses via un callable EasyAdmin `Action::setLabel(callable)`
- "Sauvegarder et modifier" → "Sauvegarder et continuer les changements" en bleu (`btn-primary`) pour Formation, Page, Works
- "Sauvegarder les modifications" → vert (`btn-success`) pour Formation, Page, Works
- Appliqué aussi sur PAGE_NEW pour la cohérence du label SAVE_AND_CONTINUE
