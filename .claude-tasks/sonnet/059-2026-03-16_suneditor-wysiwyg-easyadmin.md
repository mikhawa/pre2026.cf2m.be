# 059 — SunEditor WYSIWYG complet dans EasyAdmin

**Modèle** : Sonnet
**Justification** : Intégration multi-fichiers (Field, Controller, Stimulus, template), gestion d'upload, configuration complexe de plugins.
**Date** : 2026-03-16

## Fichiers modifiés

### Nouveaux fichiers
- `src/Field/SunEditorField.php` — Champ EasyAdmin custom (remplace TextEditorField/Trix)
- `assets/controllers/suneditor_controller.js` — Stimulus controller avec tous les plugins, barre d'outils complète, i18n français, upload d'images
- `templates/bundles/EasyAdminBundle/crud/field/suneditor.html.twig` — Template d'affichage (liste tronquée, détail modal)
- `src/Controller/Admin/MediaUploadController.php` — Endpoint POST `/admin/media/upload` (ROLE_FORMATEUR requis)
- `public/uploads/editor/` — Répertoire d'upload des images

### Fichiers modifiés
- `importmap.php` — Ajout `suneditor/dist/css/suneditor.min.css`, `suneditor/src/plugins`, `cropperjs` + dépendances v2
- `config/services.yaml` — Paramètres `uploads_editor_dir` / `uploads_editor_url` + binding du controller
- `src/Controller/Admin/PageCrudController.php` — `TextEditorField` → `SunEditorField` sur `content`
- `src/Controller/Admin/FormationCrudController.php` — `TextEditorField` → `SunEditorField` sur `description`
- `src/Controller/Admin/WorksCrudController.php` — `TextEditorField` → `SunEditorField` sur `description`

## Résumé

Remplacement de l'éditeur Trix (TextEditorField d'EasyAdmin) par SunEditor 2.47.8 dans les trois entités principales :
- `Page.content`
- `Formation.description`
- `Works.description`

**SunEditor configuré avec** :
- Tous les plugins (`suneditor/src/plugins`) — format, table, image, vidéo, audio, math, code, couleurs, etc.
- Barre d'outils complète sur 2 lignes
- Interface entièrement en français (traduction inline dans le controller)
- Upload d'images vers `/admin/media/upload` avec validation MIME et taille (5 Mo max)
- Redimensionnement d'images intégré (drag handles de SunEditor)
- Synchronisation textarea/éditeur sur `onChange` et avant soumission du formulaire
- Fonctionnement correct avec Turbo (connect/disconnect Stimulus)

**CropperJS v2 installé** (`cropperjs@2.1.0` + 11 dépendances `@cropper/*`) mais non intégré à SunEditor dans cette itération (SunEditor gère le redimensionnement en post-insertion ; une intégration CropperJS pour le crop pré-upload nécessiterait un développement personnalisé supplémentaire).

## Route d'upload

```
POST /admin/media/upload
Sécurité : ROLE_FORMATEUR
Réponse : { "result": [{ "url": "/uploads/editor/xxx.jpg", "name": "...", "size": 123 }] }
```
