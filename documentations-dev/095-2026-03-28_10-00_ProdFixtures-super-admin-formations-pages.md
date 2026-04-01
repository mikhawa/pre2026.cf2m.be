# 095 — ProdFixtures : super admin, formations et pages CMS

**Date** : 2026-03-28 10:00
**Modèle** : Sonnet

## Fichiers créés
- `src/DataFixtures/ProdFixtures.php`

## Raison
Préparer un jeu de données de production propre : pas de Faker, pas de données de test,
mot de passe administrateur via variables d'environnement.

## Ce que fait ProdFixtures

### Groupe Doctrine Fixtures
`#[AsFixture(groups: ['prod'])]` — s'exécute uniquement avec `--group=prod`.
`AppFixtures` (groupe `dev`) n'est jamais chargé en production.

### Super administrateur
Créé depuis trois variables `.env.local` :
- `PROD_ADMIN_EMAIL`
- `PROD_ADMIN_USERNAME`
- `PROD_ADMIN_PASSWORD` (haché via `UserPasswordHasherInterface`)

Si une variable est absente, une `\RuntimeException` est levée avant toute écriture en base.

### Pages CMS (publiées)
- À propos de notre centre (`about`)
- RGPD et confidentialité (`rgpd`)
- Nos valeurs et notre mission (`nos-valeurs-et-notre-mission`)

Révisions initiales créées via `RevisionService::createRevision()` après le flush.

### Formations (publiées)
- Aventure digitale
- Animateur multimédia
- Technicien PC & réseaux
- Digital Designer
- Web Developer Full Stack
- Chèques TIC

Champs `descriptionCourte` et `description` contiennent des blocs `TODO` à remplacer
par le vrai contenu avant le déploiement.

## Prérequis sur le VPS

Dans `.env.local` (jamais committé) :
```
PROD_ADMIN_EMAIL=mikhawa@cf2m.be
PROD_ADMIN_USERNAME=Mikhawa
PROD_ADMIN_PASSWORD=un-mot-de-passe-fort
```

## Commande de lancement
```bash
# Sur le VPS, depuis la racine du projet
php bin/console doctrine:fixtures:load --group=prod --env=prod
```

> ⚠️ Cette commande **vide la base** avant de charger les fixtures.
> À n'exécuter qu'une seule fois sur une base vierge ou après un backup.
