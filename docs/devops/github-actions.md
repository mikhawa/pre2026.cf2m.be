# GitHub Actions — Stratégie de déploiement

## Node.js 24 — avertissement actions/cache et actions/checkout

**Date** : 2026-04-04

GitHub dépréciera Node.js 20 sur ses runners le **2 juin 2026** (défaut Node 24) et le supprimera le **16 septembre 2026**.

`actions/checkout@v4` et `actions/cache@v4` ciblent encore Node.js 20 en interne. Cela produit deux messages successifs dans les logs CI :

1. **Avant le fix** — avertissement bloquant potentiel :
   > *"Node.js 20 actions are deprecated … may not work as expected"*

2. **Après le fix** — notice informationnelle (non bloquante) :
   > *"Node.js 20 is deprecated … are being forced to run on Node.js 24"*

### Fix appliqué

Ajout de la variable d'environnement au niveau global dans `.github/workflows/symfony.yml` :

```yaml
env:
  FORCE_JAVASCRIPT_ACTIONS_TO_NODE24: true
```

Cela force toutes les actions JavaScript du workflow à s'exécuter sous Node.js 24, conformément à la recommandation GitHub.

### Notice résiduelle

Le message *"are being forced to run on Node.js 24"* est **informatif uniquement** — le pipeline fonctionne correctement. Il disparaîtra quand les mainteneurs de `actions/checkout` et `actions/cache` publieront des versions ciblant nativement Node.js 24. Rien d'autre à faire de notre côté.

---

## À faire plus tard (améliorations possibles)

## Branches
- `main` → déploiement automatique en préprod
- `develop` → tests CI uniquement
- `feature/*` → tests CI uniquement

## Workflow préprod (.github/workflows/deploy-preprod.yml)
Étapes :
1. Checkout + cache Composer
2. composer install --no-dev --optimize-autoloader
3. Tests PHPUnit
4. SSH vers VPS → git pull + migrations + cache:clear
5. Notification (optionnel)

## Secrets GitHub requis
- SSH_PRIVATE_KEY
- VPS_HOST, VPS_USER
- DATABASE_URL_PREPROD

## Serveur cible
Ubuntu 22.04 — Nginx — PHP 8.5-FPM — MariaDB
Répertoire : /var/www/preprod/[projet]
