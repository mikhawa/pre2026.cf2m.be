# 008 — Ajout SunEditor et VichUploader dans docs/architecture/easyadmin.md

**Date** : 2026-03-03
**Fichier modifié** : `docs/architecture/easyadmin.md`

## Changements
- Ajout de `vich/uploader-bundle` dans la section Installation
- Ajout de l'installation de SunEditor via `importmap:require suneditor`
- Mise à jour des CrudControllers concernés (Formation, Works, Page, Partenaire) pour préciser l'usage de `TextareaField` + SunEditor
- Ajout de la section **Éditeur riche — SunEditor** : toolbar, upload d'images/fichiers via endpoints dédiés, langue française, intégration Stimulus
- Ajout de la section **Uploads & médias — VichUploader** : configuration `vich_uploader.yaml` avec 4 mappings (formations, works, partenaires, avatars), redimensionnement via `ImageResizeService`
- Mise à jour de la liste TODO
