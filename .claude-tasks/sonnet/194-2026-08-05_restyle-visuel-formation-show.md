# Tâche : Adaptation visuelle "raffiné white/dark" — fiche formation (formation/show)

**Numéro** : 194
**Date** : 2026-08-05
**Modèle utilisé** : Sonnet
**Justification du modèle** : Suite directe des tâches 192/193 (restyle CSS réutilisant l'architecture et les tokens déjà posés) — complexité moyenne, pas de décision d'architecture.
**Complexité** : Faible à moyenne
**Fichiers concernés** : `assets/styles/app.css`

## Contexte nécessaire
Étape 4 du plan de restyle "raffiné white/dark" (voir `.claude-tasks/sonnet/193-...md` et mémoire projet). Le mockup de référence (`datas/Site raffiné whitedark mode/symfony/templates/formation/show.html.twig`) utilise une structure très différente de la page réelle : hero band pleine largeur, grille de métadonnées (`duration`, `nextSession`, `price`, `groupSize`), liste de modules (`Collection<FormationModule>`). **Aucun de ces champs n'existe sur l'entité `Formation` réelle** (confirmé par lecture de `src/Entity/Formation.php` : seulement `title`, `slug`, `description`, `descriptionCourte`, `logo`, `status`, `colorPrimary`/`colorSecondary`, dates, relations). Reproduire la structure du mockup nécessiterait donc soit des champs fictifs (exclu par la décision utilisateur initiale), soit d'inventer un contenu factice affiché aux vrais visiteurs du site — inacceptable.

## Objectif
Vérifier que la fiche formation réelle (`templates/formation/show.html.twig`, structure en carte unique : header coloré + description + travaux réalisés + actions) hérite correctement des raffinements posés à l'étape 3 (rayons, CTA en lien texte, badges pill), et corriger toute régression visuelle.

## Constat
La page héritait déjà en grande partie des changements de `.cf2m-card` (rayon, ombre allégée) car elle réutilise le même composant que les cartes de l'accueil. Un bug a cependant été repéré par capture d'écran : le lien "← Retour aux formations" (`.cf2m-card .btn-outline-secondary`, même classes que le CTA "En savoir plus" de l'accueil) récupérait la flèche `::after` ajoutée à l'étape 3, produisant "← RETOUR AUX FORMATIONS →" (double flèche, incohérent pour un lien de retour).

## Résultat
**`assets/styles/app.css`** : la règle `::after{content:'→'}` (et le décalage au survol) est désormais scopée à `.page-home .cf2m-card .btn-outline-secondary` au lieu de `.cf2m-card .btn-outline-secondary` globalement — le style "lien plat sans bouton" reste partagé entre les deux pages (cohérence visuelle), seule la flèche ajoutée automatiquement est retirée de la fiche formation.

Vérifié par capture d'écran (Playwright/Chromium via `npx` Windows, cf. méthode documentée tâche 193) sur une formation en recrutement (`developpeur-web`, badge "Recrutement en cours" + bouton "S'inscrire") et une formation ouverte (`technicien-reseaux`, badge "Ouverte" en pill), en dark et en light : rendu cohérent avec l'accueil, aucune régression, `lint:twig` non nécessaire (aucun template modifié), page `200`.

## Suite
Étapes 5 à 7 du plan restent à faire (contact avec Turnstile, login, vérification des pages non couvertes) — voir mémoire projet associée.
