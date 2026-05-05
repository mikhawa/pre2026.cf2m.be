# 109 — Permissions ROLE_PEDAGO sur l'historique + déduplication des sauvegardes

**Date** : 2026-05-05  
**Branche** : main

---

## 1. Accès ROLE_PEDAGO aux préviews de l'historique des formations (tâche 171)

### Problème

`/admin/preview/formation-history/{id}` retournait un 403 pour `ROLE_PEDAGO`.

### Cause

`HistoryPreviewController::previewFormation()` limitait l'accès sans restriction aux seuls `ROLE_ADMIN`. Les `ROLE_PEDAGO` tombaient dans le filtre formateur (formations dont ils sont responsables uniquement).

### Fix — `src/Controller/Admin/HistoryPreviewController.php`

```php
// Avant
if (!$this->isGranted('ROLE_ADMIN')) { ... }

// Après
if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PEDAGO')) { ... }
```

### Permissions résultantes — préview formation

| Rôle | Accès |
|---|---|
| `ROLE_ADMIN` / `ROLE_SUPER_ADMIN` | Toutes les formations |
| `ROLE_PEDAGO` | Toutes les formations |
| `ROLE_FORMATEUR` | Formations dont il est responsable uniquement |

---

## 2. ROLE_PEDAGO : modification de pages sans validation + préview + restauration (tâches 172, 173)

### 2a. Modifications auto-approuvées — `src/Controller/Admin/PageCrudController.php`

```php
// Avant : seul ROLE_ADMIN passait en auto-approuvé
$isAdmin = $this->isGranted('ROLE_ADMIN');

// Après : ROLE_ADMIN et ROLE_PEDAGO passent en auto-approuvé
$isAutoApproved = $this->isGranted('CONTENT_MANAGER');
```

`CONTENT_MANAGER` est accordé à `ROLE_ADMIN` et `ROLE_PEDAGO` via `ContentManagerVoter`.

### 2b. Préview historique pages — `src/Controller/Admin/HistoryPreviewController.php`

```php
// Avant
$this->denyAccessUnlessGranted('ROLE_ADMIN');

// Après
$this->denyAccessUnlessGranted('CONTENT_MANAGER');
```

### 2c. Boutons Restaurer / Approuver / Rejeter — `templates/admin/page/historique.html.twig`

```twig
{# Avant #}
{% if is_granted('ROLE_ADMIN') %}

{# Après #}
{% if is_granted('CONTENT_MANAGER') %}
```

Les contrôleurs `approuverHistoriquePage`, `rejeterHistoriquePage` et `restaurerHistoriquePage` utilisaient déjà `CONTENT_MANAGER` — les boutons n'étaient pas rendus pour les PEDAGO.

### Permissions résultantes — pages

| Rôle | Modifier | Préview historique | Restaurer / Approuver / Rejeter |
|---|---|---|---|
| `ROLE_ADMIN` / `ROLE_SUPER_ADMIN` | Auto-approuvé | ✅ | ✅ |
| `ROLE_PEDAGO` | Auto-approuvé | ✅ | ✅ |
| `ROLE_FORMATEUR` | PENDING (validation requise) | ❌ | ❌ |

---

## 3. Bug : plusieurs "Version actuelle" dans l'historique (tâche 174)

### Symptôme

v7, v8, v9, v10 affichées simultanément comme "Version actuelle".

### Cause

```php
$isCurrent = ($snapshots[$i] === $liveSnapshot);
```

Comparaison de **contenu** : si plusieurs sauvegardes consécutives ne modifient rien, elles ont toutes le même snapshot → toutes marquées "actuelle".

### Fix — `PageCrudController`, `FormationCrudController`, `WorksCrudController`

```php
$currentFound = false;
foreach ($entries as $i => $entry) {
    $isCurrent = !$currentFound && ($snapshots[$i] === $liveSnapshot);
    if ($isCurrent) {
        $currentFound = true;
    }
    // ...
}
```

Seule la première correspondance (la plus récente, entrées triées DESC) est marquée "actuelle".

---

## 4. Déduplication des sauvegardes sans changement (tâche 175)

### Problème

Chaque sauvegarde créait une nouvelle entrée d'historique, même sans modification des champs suivis. Résultat : bruit dans l'historique, entrées "Aucun changement détecté", bug des versions multiples "actuelle".

### Solution

#### Repositories — `findLatest()`

Méthode ajoutée aux 3 repositories pour récupérer la dernière entrée (par `version DESC`, toutes statuts) :

- `PageHistoryRepository::findLatest(Page): ?PageHistory`
- `FormationHistoryRepository::findLatest(Formation): ?FormationHistory`
- `WorksHistoryRepository::findLatest(Works): ?WorksHistory`

#### `RevisionService::saveToTypedHistory()` — retourne `bool`

Avant de persister, compare le snapshot de la dernière entrée avec l'état courant :

```php
$last = $this->pageHistoryRepo->findLatest($entity);
if ($last !== null && $this->snapshotFromPageHistory($last) === $this->snapshotPage($entity)) {
    return false; // Contenu identique → pas de sauvegarde
}
```

Retourne `true` si une entrée a été créée, `false` sinon.

#### `RevisionService::createRevision()` — retourne `?Revision`

- `isCreation: true` → toujours persisté (entité nouvellement créée)
- Modification : appelle `saveToTypedHistory()` en premier ; si `false` → retourne `null`

#### Contrôleurs — gestion du `null` (chemin PENDING)

Dans les 3 contrôleurs, si `createRevision()` retourne `null` :
- Flash "Aucune modification détectée"
- Return immédiat (pas de notification, pas de flush inutile)

Pour le chemin auto-approuvé : `parent::updateEntity()` s'exécute quand même — la sauvegarde est honorée, mais sans entrée redondante en historique.

---

## Récapitulatif des fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/Admin/HistoryPreviewController.php` | Préview formation : `!isGranted('ROLE_ADMIN') && !isGranted('ROLE_PEDAGO')` ; préview page : `CONTENT_MANAGER` |
| `src/Controller/Admin/PageCrudController.php` | `updateEntity()` : `CONTENT_MANAGER` au lieu de `ROLE_ADMIN` ; gestion `null` PENDING |
| `src/Controller/Admin/FormationCrudController.php` | `updateEntity()` : gestion `null` PENDING |
| `src/Controller/Admin/WorksCrudController.php` | `updateEntity()` : gestion `null` PENDING |
| `templates/admin/page/historique.html.twig` | `is_granted('CONTENT_MANAGER')` au lieu de `ROLE_ADMIN` |
| `src/Repository/PageHistoryRepository.php` | Ajout `findLatest()` |
| `src/Repository/FormationHistoryRepository.php` | Ajout `findLatest()` |
| `src/Repository/WorksHistoryRepository.php` | Ajout `findLatest()` |
| `src/Service/RevisionService.php` | `saveToTypedHistory()` → `bool` + dedup ; `createRevision()` → `?Revision` |

## Tâches associées

- `.claude-tasks/haiku/171-2026-05-05_preview-formation-history-role-pedago.md`
- `.claude-tasks/haiku/172-2026-05-05_pedago-pages-sans-validation-historique.md`
- `.claude-tasks/haiku/173-2026-05-05_pedago-restaurer-page-historique-template.md`
- `.claude-tasks/haiku/174-2026-05-05_fix-version-actuelle-multiple-historique.md`
- `.claude-tasks/sonnet/175-2026-05-05_deduplication-historique-sauvegardes-identiques.md`
