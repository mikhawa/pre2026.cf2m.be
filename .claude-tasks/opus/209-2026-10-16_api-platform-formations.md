# Tâche : Finalisation d'API Platform et ressource pilote « Formations »

**Numéro** : 209
**Date** : 2026-10-16
**Modèle utilisé** : Opus
**Justification du modèle** : choix d'architecture de l'API (sécurité de l'exposition des données, session vs stateless)
**Complexité** : Moyenne
**Fichiers concernés** : `composer.json`, `composer.lock`, `symfony.lock`, `config/bundles.php`, `config/reference.php`,
`config/packages/api_platform.yaml`, `config/routes/api_platform.yaml`, `src/ApiResource/FormationResource.php`,
`src/State/FormationProvider.php`, `tests/State/FormationProviderTest.php`, `docs/architecture/decision-ilots-react.md`

## Contexte nécessaire
Question : API Platform côté frontend ou backend ? → backend uniquement (paquets Composer). L'utilisateur avait lancé
`composer require api-platform/symfony api-platform/doctrine-orm` (v5.0.3, contraintes `*`, recette par défaut).

## Résultat
- Contraintes `^5.0` dans `composer.json`.
- `api_platform.yaml` : titre « API CF2m », formats jsonld + json, `stateless: false` (session), pagination 12,
  documentation désactivée en prod.
- Ressource DTO `FormationResource` (lecture seule, identifiant = slug) + `FormationProvider` réutilisant
  `FormationRepository::findAllPublished()` / `findOneBySlug()` ; brouillon → 404.
- 4 tests unitaires du provider.
- Vérifié en local via le relais Vite : liste OK, détail 200, slug inconnu et brouillon 404, `/api/docs` 200, `/api/statut` OK.
