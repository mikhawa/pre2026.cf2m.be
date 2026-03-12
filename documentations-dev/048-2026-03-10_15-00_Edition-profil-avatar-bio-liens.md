# 048 — Édition du profil utilisateur (avatar, biographie, liens)

**Date** : 2026-03-10 15:00
**Fichiers** :
- `src/Form/ProfileEditType.php` (nouveau)
- `src/Controller/ProfileController.php`
- `templates/profil/index.html.twig`
- `assets/styles/app.css`

## Résumé

Tout utilisateur connecté peut désormais modifier son profil depuis `/profil`.

## Formulaire `ProfileEditType`
- `avatarFile` → `VichImageType` (upload + suppression)
- `biography` → `TextareaType` (600 car. max, contrainte déjà sur l'entité)
- `externalLink1/2/3` → `UrlType` (validation URL déjà sur l'entité)

## Controller
```php
$form->handleRequest($request);
if ($form->isSubmitted() && $form->isValid()) {
    $em->flush(); // Vich gère automatiquement le déplacement du fichier
    $this->addFlash('success', 'Votre profil a été mis à jour.');
    return $this->redirectToRoute('app_profile');
}
```

## Template
- Carte "lecture" en haut (identité, bio, liens, formations)
- Carte "Modifier mon profil" en bas avec le formulaire
- Prévisualisation de l'avatar avant upload (FileReader JS, vanilla)
- Flash success Bootstrap dismissible

## Notes
- Vich gère automatiquement le déplacement et le nommage du fichier
- `enctype="multipart/form-data"` obligatoire sur le formulaire
