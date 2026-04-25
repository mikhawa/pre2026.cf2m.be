---
modèle: haiku
justification: Ajout de données réelles dans des fichiers de fixtures, pas de logique métier
---

## Tâche 162 — Ajout des 4 partenaires réels dans les fixtures

**Date** : 2026-04-25

### Source
Partenaires récupérés depuis https://production.cf2m.be/ (section "Nos partenaires").

### Partenaires ajoutés
1. ACTIRIS Formation — https://www.actiris.brussels/fr/citoyens/
2. Bruxelles Formation — https://www.bruxellesformation.brussels/
3. Fédération Wallonie Bruxelles — https://www.federation-wallonie-bruxelles.be/
4. Fond Social Européen — https://fse.be/

### Fichiers modifiés
- `src/DataFixtures/AppFixtures.php` — 4 `createOne()` explicites ajoutés avant les aléatoires (réduits de 6 à 2)
- `src/DataFixtures/ProdFixtures.php` — import `Partenaire` + section partenaires avec `new Partenaire()` direct (sans factory)

### Résultat
En dev (`AppFixtures`) : 4 vrais + 2 aléatoires actifs + 2 inactifs.
En prod (`ProdFixtures`) : 4 vrais partenaires actifs, sans logo (à uploader via EasyAdmin).
