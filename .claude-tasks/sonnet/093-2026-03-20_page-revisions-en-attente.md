# Tâche 093 — Page dédiée "Révisions en attente"

**Modèle** : Sonnet
**Justification** : Nouveau controller action + template + repositories

## Fichiers modifiés / créés
- `src/Repository/FormationHistoryRepository.php` — ajout `findAllPending()`, `findByVersion()`
- `src/Repository/PageHistoryRepository.php` — idem
- `src/Repository/WorksHistoryRepository.php` — idem
- `src/Controller/Admin/DashboardController.php` — nouvelle action `revisionsPendantes()` + menu item
- `templates/admin/revisions-en-attente.html.twig` — nouveau template
- `templates/profil/index.html.twig` — lien mis à jour vers la nouvelle route

## Résumé
Création d'une page `/admin/revisions-en-attente` (route `admin_revisions_en_attente`) accessible uniquement aux admins.

### Fonctionnalités
- Liste toutes les révisions PENDING (Formations + Pages + Works) en une seule page
- Affiche le diff de chaque révision par rapport à la version précédente
- Boutons "Approuver & appliquer" et "Rejeter" directement depuis cette page
- Retour vers la page elle-même après chaque action (returnUrl)
- Lien "Historique complet" vers la page historique de l'entité
- Badge total dans le menu "Révisions en attente" (Interactions)
- Lien depuis la bannière d'alerte du profil admin

## Résultat
✅ Un admin peut valider ou rejeter toutes les révisions en attente depuis une seule page centralisée.
