# 167 — Mise à jour en temps réel de la lecture des messages de contact (admin)

**Modèle** : Sonnet  
**Justification** : Même complexité que tâche 166 (inscription) — controller AJAX + module JS  
**Date** : 2026-05-04

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/Admin/ContactMessageAjaxController.php` | Créé |
| `assets/contact_read.js` | Créé |
| `assets/admin.js` | Import `contact_read.js` ajouté |

## Résumé

Même mécanique que pour les inscriptions (tâche 166) appliquée aux messages de contact.

- **Endpoint AJAX** `GET /admin/contact-message/{id}/lecture-info` : retourne `readBy` et `unreadCount`
- **Module JS** `contact_read.js` : event delegation sur `td[data-column="read"]`, attend 600ms, met à jour `td[data-column="readBy"]` et le badge du menu (`href*="/admin/contact-message"`)
- Pas de colonne `readAt` (l'entité `ContactMessage` n'a pas de champ date de lecture, contrairement à `Inscription`)
