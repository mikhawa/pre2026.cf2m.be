# 064 — Révision initiale automatique pour Pages et Works

**Date** : 2026-03-17
**Modèle** : Sonnet
**Justification** : Extension du système de révisions existant

## Changements

### `src/EventListener/ContentRevisionListener.php` (remplace `FormationRevisionListener.php`)

Listener unifié pour Formation, Page et Works :
- Collecte les entités dans `postPersist`
- Résout l'auteur et crée les révisions dans `postFlush`
- Pour Page/Works : requête DBAL directe sur `page_user`/`works_user`
  (contourne le lazy-loading qui échoue avec les proxies Foundry)

### `src/DataFixtures/AppFixtures.php`

Création explicite des révisions initiales pour Pages et Works après chaque
groupe de flush, car avec `flush_once: true` (Foundry), les tables de jonction
ManyToMany sont insérées dans un flush ULTÉRIEUR à la création de l'entité.

Révisions Formation : gérées par le listener (createdBy sur la ligne même).
Révisions Page/Works en fixtures : gérées explicitement via `$revisionService`.

## Résultat

Après `doctrine:fixtures:load` :
```
formation → 8 révisions ✅
page      → 3 révisions ✅
works     → 14 révisions ✅
```
