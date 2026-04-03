# Tâche 130 — Ajout du champ téléphone à l'entité Inscription

**Date** : 2026-04-03  
**Modèle** : Haiku  
**Justification** : Ajout simple d'un champ CRUD obligatoire sur l'entité Inscription, sans dépendances complexes.

## Fichiers modifiés

1. `src/Entity/Inscription.php` — Propriété + getter/setter
2. `src/Form/InscriptionType.php` — Champ TelType dans le formulaire
3. `migrations/Version20260403110000.php` — Migration BDD (CREATE)
4. `templates/formation/show.html.twig` — Affichage du champ dans la modale d'inscription
5. `templates/emails/inscription_admin.html.twig` — Bloc TÉLÉPHONE dans l'email admin
6. `templates/emails/inscription_confirmation.html.twig` — Ligne téléphone dans le tableau récapitulatif
7. `src/Controller/Admin/InscriptionCrudController.php` — Field téléphone dans EasyAdmin

## Résumé des changements

- Entité : propriété `telephone` (VARCHAR 30, obligatoire) + validations `#[Assert\NotBlank]`
- Formulaire : `TelType::class` avec placeholder `+32 4xx xx xx xx`
- BDD : migration MariaDB `ALTER TABLE inscription ADD telephone VARCHAR(30) NOT NULL DEFAULT '' AFTER email`
- Frontend : 
  - Modal inscription : `telephone` (col-md-8) + `age` (col-md-4) sur même ligne, `email` seul au-dessus
  - Email admin : bloc TÉLÉPHONE inséré après bloc E-MAIL
  - Email confirmation : ligne téléphone insérée après ligne E-mail dans le tableau récapitulatif
- EasyAdmin : Field `TextField` pour `telephone`, `hideOnIndex()`

## Résultat

Tous les fichiers modifiés correctement. Migrations créée et prête à l'exécution.

```bash
docker compose exec php php bin/console doctrine:migrations:execute --up 'DoctrineMigrations\Version20260403110000' --no-interaction
```

Champ entièrement intégré dans le workflow d'inscription (formulaire → BDD → emails → back-office).
