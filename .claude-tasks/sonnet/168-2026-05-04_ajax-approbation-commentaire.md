# 168 — Approbation commentaires : champs approvedBy/approvedAt + badge menu + temps réel

**Modèle** : Sonnet  
**Justification** : Même complexité que tâches 166 et 167 — entité + migration + controller AJAX + JS  
**Date** : 2026-05-04

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Entity/Comment.php` | Ajout champs `approvedBy` (ManyToOne User) et `approvedAt` (DateTimeImmutable) |
| `migrations/Version20260504100000.php` | Créé — colonnes `approved_by_id` et `approved_at` sur table `comment` |
| `src/Repository/CommentRepository.php` | Ajout méthode `countUnapproved()` |
| `src/Controller/Admin/CommentCrudController.php` | Ajout champs `approvedBy`/`approvedAt` + hook `updateEntity()` |
| `src/Controller/Admin/DashboardController.php` | Injection `CommentRepository` + badge Commentaires |
| `src/Controller/Admin/CommentAjaxController.php` | Créé — endpoint `GET /admin/comment/{id}/approbation-info` |
| `assets/comment_approve.js` | Créé — module JS temps réel |
| `assets/admin.js` | Import `comment_approve.js` ajouté |
| `translations/messages.fr.yaml` | Ajout `Approuvé par` et `Approuvé le` |

## Résumé

Même mécanique que pour les inscriptions (166) et messages de contact (167) :

- **Entité** : deux nouveaux champs — `approvedBy` (qui a approuvé) et `approvedAt` (quand)
- **Hook `updateEntity()`** : si `isApproved = true` → set `approvedBy` (user connecté) + `approvedAt` (now) ; si false → null les deux
- **Badge menu** : compte en temps réel les commentaires non approuvés (`countUnapproved()`) affiché sur l'entrée "Commentaires" du menu latéral
- **Endpoint AJAX** `GET /admin/comment/{id}/approbation-info` : retourne `approvedBy`, `approvedAt`, `approvedAtIso`, `unapprovedCount`
- **JS** : event delegation sur `td[data-column="approved"]`, attend 600ms, met à jour `approvedBy` et `approvedAt` dans la ligne
- Badge selector : `href*="/admin/comment"` (distinct de `/admin/contact-message`)
