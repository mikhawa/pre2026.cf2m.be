# 063 — Correction listener révision initiale : postPersist → postFlush

**Date** : 2026-03-17
**Modèle** : Sonnet
**Justification** : Correction d'un bug de listener Doctrine — niveau service métier

## Problème

`FormationRevisionListener` était enregistré sur `postPersist` et appelait
`$this->em->persist($revision)`, mais 0 révision n'était créée après
`doctrine:fixtures:load`. Le listener était bien enregistré (confirmé via
`debug:container --tag=doctrine.event_listener`).

**Cause racine** : Avec Foundry `flush_once: true`, les entités persistées via
`$em->persist()` à l'intérieur d'un handler `postPersist` entrent dans le UoW
mais ne sont jamais flushées — Foundry ne déclenche pas de flush supplémentaire
après le cycle principal.

## Solution

Pattern "collecte puis flush différé" :

1. `postPersist` → mémorise la formation dans `$pendingRevisions` (sans persister)
2. `postFlush` → crée toutes les révisions en attente + déclenche un second flush

Le second flush ne crée pas de boucle infinie car `$pendingRevisions` est vidé
AVANT la création des révisions.

## Fichiers modifiés

- `src/EventListener/FormationRevisionListener.php`
  - Ajout de l'attribut `#[AsDoctrineListener(event: Events::postFlush)]`
  - Ajout de la propriété `$pendingRevisions`
  - `postPersist` : collecte uniquement (plus de `persist`)
  - `postFlush` : création + flush des révisions

## Résultat

Après `doctrine:fixtures:load` :
```
SELECT COUNT(*) FROM revision WHERE entity_type = 'formation' → 8
```
8 révisions créées pour 8 formations. ✅
