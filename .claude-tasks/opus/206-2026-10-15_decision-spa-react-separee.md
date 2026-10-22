# Tâche : Passage de l'option A (îlots React) à l'option B (SPA React séparée, Symfony en backend)

**Numéro** : 206
**Date** : 2026-10-15
**Modèle utilisé** : Opus
**Justification du modèle** : décision d'architecture majeure (critère « Architecture » de `.claude/models.md`)
**Complexité** : Complexe
**Fichiers concernés** : `docs/architecture/decision-ilots-react.md`, `docs/architecture/overview.md`, `docs/architecture/frontend.md`, `CLAUDE.md`

## Contexte nécessaire
Le 2026-10-02, l'option A (îlots React dans Twig via Symfony UX React) avait été retenue et mise en place.
Après nouvelle discussion d'équipe le 2026-10-15, l'option B est retenue : SPA React autonome, Symfony conservé
comme backend (API JSON, sécurité, BDD, EasyAdmin).

## Objectif
Mettre à jour la décision d'architecture et les documents qui y font référence. Aucun code applicatif modifié.

## Résultat
- `decision-ilots-react.md` réécrit : option B retenue, A marquée « remplacée », points à trancher
  (authentification, CSRF, SEO, hébergement, formulaires) avec recommandations, conséquences sur l'existant,
  plan de migration en 5 étapes, historique.
- `overview.md`, `frontend.md`, `CLAUDE.md` : mention de la décision, l'état actuel (îlots) restant décrit
  jusqu'à la fin de la migration.
- Prochaine étape proposée : squelette `frontend/` (Vite + React 19) + service Docker + build en CI.
