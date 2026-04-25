---
modèle: haiku
justification: Modification CRUD simple, 3 champs à corriger
---

## Tâche 161 — Désactivation des liens mailto dans EasyAdmin

**Date** : 2026-04-25

### Fichiers modifiés
- `src/Controller/Admin/ContactMessageCrudController.php`
- `src/Controller/Admin/InscriptionCrudController.php`
- `src/Controller/Admin/UserCrudController.php`

### Résumé
`EmailField` génère un `<a href="mailto:...">` en liste/détail.
Solution : split en deux déclarations par contrôleur :
- `EmailField::new('email')->onlyOnForms()` → conserve la validation HTML5 dans les formulaires
- `TextField::new('email')->hideOnForm()` → texte brut sans lien en index/détail

### Résultat
Les adresses e-mail s'affichent en texte simple (non cliquable) dans les vues liste et détail des trois sections admin concernées.
