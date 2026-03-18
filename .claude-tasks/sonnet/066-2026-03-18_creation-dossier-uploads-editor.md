# 066 — Création du dossier uploads/editor pour SunEditor

**Modèle** : Sonnet
**Justification** : Diagnostic de permissions Docker + création de dossiers

## Problème
L'upload d'images via SunEditor retournait :
`Unable to create the "/var/www/html/public/uploads/editor" directory`

## Cause
`public/` appartient à uid 1000 (hôte, mikhawa) avec permissions `755`.
PHP tourne en tant que `www-data` (uid 33) et ne peut donc pas créer de sous-dossiers dans `public/`.

## Fichiers créés
- `public/uploads/` — dossier créé avec chmod 777
- `public/uploads/editor/` — dossier créé avec chmod 777
- `public/uploads/.gitignore` — ignore les fichiers uploadés, conserve la structure
- `public/uploads/editor/.gitignore` — idem pour le sous-dossier SunEditor

## Résultat
Le container `pre_cf2m_php` peut maintenant écrire dans `/var/www/html/public/uploads/editor`.
Les dossiers sont versionnés (via .gitignore) pour que la structure existe après un clone.
