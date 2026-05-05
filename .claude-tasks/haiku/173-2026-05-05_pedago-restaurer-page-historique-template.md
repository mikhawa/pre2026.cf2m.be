---
modèle: haiku
date: 2026-05-05
justification: Correction d'une condition Twig dans un template — changement minimal
---

## Tâche

Permettre à `ROLE_PEDAGO` de voir et utiliser les boutons Restaurer / Approuver / Rejeter dans l'historique des pages.

## Cause

Le template `templates/admin/page/historique.html.twig` cachait les boutons d'action derrière `{% if is_granted('ROLE_ADMIN') %}`. Les contrôleurs correspondants utilisaient déjà `CONTENT_MANAGER` (qui couvre ROLE_PEDAGO), mais les boutons n'étaient jamais affichés.

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `templates/admin/page/historique.html.twig` | `is_granted('ROLE_ADMIN')` → `is_granted('CONTENT_MANAGER')` |
