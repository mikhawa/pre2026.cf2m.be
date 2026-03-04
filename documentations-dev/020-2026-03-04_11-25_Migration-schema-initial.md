# 020 — Migration initiale du schéma BDD

**Date** : 2026-03-04 11:25
**Modèle** : Haiku

## Fichier créé
- `migrations/Version20260304112351.php`

## Résumé
Première migration Doctrine générée depuis les entités PHP.

### Tables créées (9 métier + 5 jointure)
| Table | Type |
|-------|------|
| `user` | Entité |
| `formation` | Entité |
| `works` | Entité |
| `comment` | Entité |
| `rating` | Entité |
| `inscription` | Entité |
| `contact_message` | Entité |
| `page` | Entité |
| `partenaire` | Entité |
| `formation_user` | Jointure ManyToMany |
| `works_user` | Jointure ManyToMany |
| `page_user` | Jointure ManyToMany |
| `comment_rating` | Jointure ManyToMany |
| `rating_works` | Jointure ManyToMany |
| `messenger_messages` | Symfony Messenger |

## Commande pour appliquer
```bash
docker compose exec php php bin/console doctrine:migrations:migrate
```
