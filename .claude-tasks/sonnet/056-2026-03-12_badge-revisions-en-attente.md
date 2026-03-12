# 056 — Badge révisions en attente (profil, navbar, EasyAdmin)

**Modèle** : Sonnet
**Justification** : Ajout d'indicateurs visuels dans plusieurs templates et services

## Fichiers modifiés
- `src/Twig/NavigationExtension.php` — injection `RevisionRepository`, ajout fonction `pending_revisions_count()`
- `src/Controller/Admin/DashboardController.php` — injection `RevisionRepository`, badge sur le menu "Révisions"
- `templates/base.html.twig` — badge rouge sur "Administration" dans le dropdown (ROLE_ADMIN uniquement)
- `templates/profil/index.html.twig` — alerte cliquable vers `/admin/revision` (ROLE_ADMIN uniquement)

## Résumé
- **Navbar frontend** : badge rouge avec le compte de révisions en attente sur le lien "Administration" du dropdown utilisateur, visible uniquement pour ROLE_ADMIN
- **Page profil** : alerte orange cliquable renvoyant vers la liste des révisions EasyAdmin, visible uniquement pour ROLE_ADMIN
- **Sidebar EasyAdmin** : badge rouge `danger` sur le menu "Révisions" affiché uniquement si count > 0
