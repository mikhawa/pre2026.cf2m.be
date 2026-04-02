# 096 — Prévisualisation des versions d'historique (Formation / Page / Works)

**Date :** 2026-04-02  
**Modèle Claude utilisé :** Sonnet (tâche `.claude-tasks/sonnet/127`)

---

## Contexte

Les pages d'historique (formation, page, works) affichent une timeline de toutes les versions enregistrées sous forme de diff. Il n'était pas possible de voir le rendu réel d'une version (contenu HTML, logo, couleurs, etc.) sans appliquer/restaurer cette version sur l'entité live.

## Objectif

Ajouter un bouton **"Prévisualiser v{{ n }}"** sur chaque entrée de la timeline, ouvrant dans un nouvel onglet (`_blank`) une page standalone présentant tous les champs du snapshot de cette version.

---

## Fichiers créés

### `src/Controller/Admin/HistoryPreviewController.php`

Nouveau controller avec 3 routes, toutes sous le préfixe `/admin/preview/` :

| Route | Nom Symfony | Accès minimum |
|-------|-------------|---------------|
| `GET /admin/preview/formation-history/{id}` | `admin_preview_history_formation` | `ROLE_FORMATEUR` |
| `GET /admin/preview/page-history/{id}` | `admin_preview_history_page` | `ROLE_ADMIN` |
| `GET /admin/preview/works-history/{id}` | `admin_preview_history_works` | `ROLE_FORMATEUR` |

**Logique de sécurité :**
- Formation et Works : un formateur non-admin ne peut prévisualiser que les versions liées à ses propres formations (via `$formation->getResponsables()->contains($user)`).
- Page : réservé aux admins (cohérent avec la page d'historique des pages).

---

### `templates/admin/history_preview/formation.html.twig`

Template HTML standalone (sans héritage EasyAdmin ni `base.html.twig`), Bootstrap 5 via CDN.

**Champs affichés :**
- Bandeau fixe : numéro de version, date/heure, auteur, badge statut (approuvée / en attente / rejetée), bouton "Fermer"
- Hero coloré (couleur primaire via CSS `var(--preview-primary)`) avec logo Vich (`/uploads/formation-logos/`) et titre
- Description courte (texte brut)
- Description complète (`|raw` — HTML SunEditor rendu)
- Informations latérales : statut, date de publication, couleur primaire (swatch + code hex), couleur secondaire (swatch + code hex)
- Liste des responsables
- Note de révision (si présente), colorée en rouge si rejetée

---

### `templates/admin/history_preview/page.html.twig`

**Champs affichés :**
- Bandeau (version, date, auteur, statut)
- Titre, slug, statut, date de publication, auteurs
- Contenu complet (`|raw` — HTML SunEditor rendu)
- Note de révision

---

### `templates/admin/history_preview/works.html.twig`

**Champs affichés :**
- Bandeau (version, date, auteur, statut)
- Titre, slug, statut, date de publication
- Description (`|raw` — HTML SunEditor rendu)
- Formation parente (titre + swatch couleur primaire)
- Liste des auteurs (users)
- Note de révision

---

## Fichiers modifiés

### `templates/admin/formation/historique.html.twig`
### `templates/admin/page/historique.html.twig`
### `templates/admin/works/historique.html.twig`

Les 3 templates ont été restructurés de la même façon :

**Avant :** le footer avec les boutons d'action n'existait que si `hasActions` était vrai (au moins un bouton approuver/restaurer disponible).

**Après :** un footer est toujours rendu, contenant :
1. Bouton **"Prévisualiser v{{ n }}"** — toujours visible, `target="_blank"` (`btn-outline-secondary`)
2. Boutons d'approbation / rejet / restauration — conservés sous les mêmes gardes de permission qu'avant (`FORMATION_APPROVE`, `ROLE_ADMIN`, `WORKS_APPROVE`)

---

## Architecture des templates preview

```
templates/
└── admin/
    └── history_preview/
        ├── formation.html.twig   ← logo + couleurs + HTML description
        ├── page.html.twig        ← HTML content
        └── works.html.twig       ← HTML description + formation parente
```

---

## Sécurité

- Les routes de preview sont sous `/admin/`, donc couvertes par le firewall EasyAdmin.
- Aucun token d'accès temporaire : la session authentifiée Symfony suffit.
- Le voter custom n'est pas recalculé dans le preview (lecture seule, pas d'action).
- Le contenu HTML est rendu tel quel (`|raw`) : il provient exclusivement de SunEditor via les snapshots en base — pas de saisie utilisateur externe.

---

## Test manuel

1. Aller dans l'admin → Formation → Historique d'une formation
2. Chaque version affiche le bouton **"Prévisualiser v1"**, **"Prévisualiser v2"**, etc.
3. Le clic ouvre un nouvel onglet avec le snapshot complet : logo, couleurs, HTML rendu, métadonnées
4. Idem pour Pages et Works
