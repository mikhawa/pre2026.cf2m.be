# 114 — Bloquer accès Formations pour ROLE_STAGIAIRE

**Modèle** : Haiku
**Justification** : Correction de sécurité simple (garde Twig + deny controller).

## Fichiers modifiés
- `templates/admin/dashboard.html.twig`
- `src/Controller/Admin/FormationCrudController.php`

## Résumé

`setPermission()` d'EasyAdmin ne bloque que les boutons UI, pas les URLs directes.
Un stagiaire pouvait accéder à `/admin/formation` malgré l'absence de lien dans le menu.

### Corrections
- Dashboard : carte Formations entourée de `{% if is_granted('ROLE_FORMATEUR') %}`
- `FormationCrudController.configureCrud()` : `denyAccessUnlessGranted('ROLE_FORMATEUR')`
  → EasyAdmin appelle `configureCrud()` pour toutes les actions (index, edit, new, detail),
    ce qui bloque l'accès URL direct pour tout rôle inférieur à ROLE_FORMATEUR.
