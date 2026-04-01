---
modèle: sonnet
date: 2026-03-28
---

# 121 — Création des fixtures de production (ProdFixtures)

## Justification du modèle
Création d'une nouvelle classe Fixture avec injection de services, gestion des env vars et
logique de création d'entités — Sonnet adapté.

## Fichiers créés
- `src/DataFixtures/ProdFixtures.php`

## Résumé
Classe `ProdFixtures` avec groupe `prod` (`#[AsFixture(groups: ['prod'])]`).

Crée sans Foundry (non disponible hors dev/test) :
- 1 super administrateur dont email/username/password viennent de `.env.local`
- 3 pages CMS (about, rgpd, nos-valeurs) avec révisions initiales
- 6 formations réelles (published) avec blocs TODO pour le contenu

## Variables d'env requises sur le VPS (dans `.env.local`)
```
PROD_ADMIN_EMAIL=mikhawa@cf2m.be
PROD_ADMIN_USERNAME=Mikhawa
PROD_ADMIN_PASSWORD=un-mot-de-passe-fort
```

## Lancement sur le VPS
```bash
php bin/console doctrine:fixtures:load --group=prod --env=prod
```

## À faire
Remplacer les blocs `<!-- TODO -->` et les chaînes `TODO :` dans `ProdFixtures.php`
par le vrai contenu HTML des pages et formations.
