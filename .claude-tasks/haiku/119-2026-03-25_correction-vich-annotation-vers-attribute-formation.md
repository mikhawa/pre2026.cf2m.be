# 119 — Correction dépreciation VichUploader : Annotation → Attribute dans Formation

**Modèle** : Haiku
**Justification** : Remplacement d'un import, aucune logique métier

## Fichiers modifiés
- `src/Entity/Formation.php` — `Mapping\Annotation` → `Mapping\Attribute`

## Résumé
`Formation.php` utilisait encore l'ancien namespace `Vich\UploaderBundle\Mapping\Annotation`
déprécié depuis vich/uploader-bundle 2.9. Remplacé par `Vich\UploaderBundle\Mapping\Attribute`.
`User.php` était déjà correct.

## Résultat
Dépréciations supprimées des logs lors du `cache:clear --env=prod`.
