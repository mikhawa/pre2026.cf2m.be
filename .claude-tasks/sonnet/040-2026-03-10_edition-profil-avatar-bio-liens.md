# 040 — Édition du profil : avatar, biographie, liens externes

**Date** : 2026-03-10
**Modèle** : Sonnet (formulaire + controller + template + VichUploader)

## Fichiers créés/modifiés
- `src/Form/ProfileEditType.php` (nouveau)
- `src/Controller/ProfileController.php` (refonte)
- `templates/profil/index.html.twig` (ajout formulaire)
- `assets/styles/app.css` (styles .cf2m-form-label, .cf2m-avatar-upload)

## Résumé

Permet à tout utilisateur connecté de modifier son profil depuis `/profil` :
- **Avatar** : upload via `VichImageType` avec prévisualisation JS inline, suppression possible
- **Biographie** : textarea 600 car. max
- **3 liens externes** : UrlType avec placeholder descriptifs

Le formulaire est en bas de page (carte séparée "Modifier mon profil"). La carte du haut reste en lecture seule.

Flash `success` affiché après save. Redirect POST/GET après soumission.

## Détails techniques
- `ProfileController` injecte `Request` + `EntityManagerInterface`
- Cast explicite `/** @var User $user */` sur `getUser()`
- `enctype="multipart/form-data"` sur le formulaire (nécessaire pour l'upload)
- Prévisualisation avatar en JS vanilla (FileReader API), sans dépendance externe
