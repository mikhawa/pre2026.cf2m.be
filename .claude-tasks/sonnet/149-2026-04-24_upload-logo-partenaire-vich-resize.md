# 149 — Upload logo partenaire avec redimensionnement automatique

**Modèle** : Sonnet
**Justification** : Event subscriber + intégration VichUploader + migration — complexité service métier

## Fichiers modifiés

- `config/packages/vich_uploader.yaml` — ajout mapping `partenaire_logo`
- `src/Entity/Partenaire.php` — ajout `#[Vich\Uploadable]`, `logoFile`, `updatedAt`, contraintes Assert\Image
- `src/Controller/Admin/PartenaireCrudController.php` — remplacement TextField logo par VichImageType + ImageField affichage
- `src/EventSubscriber/PartenaireLogoResizeSubscriber.php` — créé (POST_UPLOAD → GD resize max 400×300 px)
- `migrations/Version20260424044040.php` — ADD COLUMN `updated_at` sur `partenaire`
- `public/uploads/partenaire-logos/` — répertoire créé

## Résumé

Ajout d'un upload d'image redimensionnable pour le logo partenaire dans EasyAdmin :
- Mapping VichUploader `partenaire_logo` → `/uploads/partenaire-logos/`
- `logoFile` (non mappé Doctrine) + `logo` (nom fichier) + `updatedAt` sur l'entité
- `PartenaireLogoResizeSubscriber` écoute `vich_uploader.post_upload` : si l'objet est un `Partenaire`, redimensionne via GD à 400×300 max (ratio conservé, transparence PNG/GIF préservée)
- Dans EasyAdmin : `VichImageType` (formulaire) + `ImageField` (affichage liste/détail)
- Validation : max 2 Mo, formats JPEG/PNG/GIF/WebP/SVG

## Résultat

Migration exécutée avec succès. Le champ Logo dans `/admin/partenaire/` permet désormais l'upload et affiche un aperçu ; les images > 400×300 sont automatiquement redimensionnées après upload.
