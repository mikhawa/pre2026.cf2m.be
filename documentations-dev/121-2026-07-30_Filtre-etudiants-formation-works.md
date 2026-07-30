# 121 — Filtrage des étudiants par formation sur l'édition d'un Work

**Date :** 2026-07-30
**Commit :** `90f2ec1`
**Branche :** `feature/26-update-stagiaires-to-admin`

---

## Contexte

Sur la page d'édition d'un `Work` en back-office (ex. `/admin/works/821/edit`), le champ « Étudiants » (`AssociationField` sur la relation `Works::users`) proposait tous les utilisateurs ayant `ROLE_STAGIAIRE`, sans tenir compte de la `Formation` à laquelle appartient le Work. Un formateur ou un admin pouvait donc associer un stagiaire n'ayant aucun rattachement à la formation concernée.

Depuis l'introduction de l'entité pivot `FormationStagiaire` (voir `documentations-dev/119-2026-07-02_Gestion-stagiaires-par-formation.md`), l'appartenance réelle d'un stagiaire à une formation est connue en base — il devenait cohérent de s'en servir pour restreindre ce champ.

---

## Fonctionnalité ajoutée

Le `setQueryBuilder` du champ `AssociationField::new('users', 'Étudiants')` dans `WorksCrudController` restreint désormais la liste :
- toujours filtrée par rôle (`entity.roles LIKE '%ROLE_STAGIAIRE%'`, comportement existant inchangé) ;
- **et**, si le Work en cours d'édition a déjà une formation associée, filtrée en plus aux utilisateurs ayant une entrée `FormationStagiaire` pour cette formation.

La formation courante est récupérée via `$this->getContext()->getEntity()->getInstance()` (l'instance du `Works` en cours d'édition dans le contexte EasyAdmin), puis jointe à `FormationStagiaire` par une jointure DQL arbitraire (`Doctrine\ORM\Query\Expr\Join`, sans association mappée sur `User`) :

```php
$qb->join(FormationStagiaire::class, 'fs', Join::WITH, 'fs.user = entity AND fs.formation = :formation')
    ->setParameter('formation', $formation);
```

Cas particulier : à la création d'un Work (`formation` pas encore renseignée), le filtre par formation ne s'applique pas — comportement identique à l'existant (tous les stagiaires).

---

## Fichiers modifiés

- `src/Controller/Admin/WorksCrudController.php` — ajout de la jointure sur `FormationStagiaire` dans le `queryBuilder` du champ `users`, import de `App\Entity\FormationStagiaire` et `Doctrine\ORM\Query\Expr\Join`.

---

## Vérifications effectuées

- `doctrine:schema:validate --skip-sync` → mapping correct
- Requête DQL équivalente exécutée manuellement sur la formation #455 (formation du Work #811) : les 6 utilisateurs retournés correspondent exactement aux lignes `formation_stagiaire` de cette formation
- `bin/phpunit` → 177 tests, 404 assertions, aucune régression
- `php-cs-fixer --dry-run` → aucune correction nécessaire

---

## Traçabilité

Implémenté par le modèle **Sonnet** (controller métier, logique de requête Doctrine — sans enjeu d'architecture ou de sécurité majeur). Détails complets : `.claude-tasks/sonnet/187-2026-07-30_filtre-etudiants-formation-works.md`.