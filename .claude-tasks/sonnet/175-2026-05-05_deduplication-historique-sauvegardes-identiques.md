---
modèle: sonnet
date: 2026-05-05
justification: Logique de déduplication dans le service central + impact sur plusieurs contrôleurs
---

## Tâche

Empêcher la création d'entrées d'historique redondantes quand aucun champ n'a changé depuis la dernière version.

## Cause du problème

Chaque sauvegarde (même sans modification) créait une nouvelle entrée — d'où les "Aucun changement détecté" multiples en historique, et le bug "4 versions actuelles".

## Solution

### 1. Repositories — ajout de `findLatest()`

Méthode ajoutée dans les 3 repositories (retourne l'entrée la plus récente par numéro de version) :

| Repository | Méthode |
|---|---|
| `PageHistoryRepository` | `findLatest(Page): ?PageHistory` |
| `FormationHistoryRepository` | `findLatest(Formation): ?FormationHistory` |
| `WorksHistoryRepository` | `findLatest(Works): ?WorksHistory` |

### 2. `RevisionService::saveToTypedHistory()` — retourne `bool`

Avant de persister, compare le snapshot de la dernière entrée existante avec celui de l'entité courante. Si identiques → `return false` (rien persisté).

### 3. `RevisionService::createRevision()` — retourne `?Revision`

- Si `isCreation: true` → toujours créer (pas de vérification)
- Sinon : appelle `saveToTypedHistory()` en premier ; si `false` → retourne `null` immédiatement

### 4. Contrôleurs — gestion du `null`

Dans le chemin PENDING (`!$isAutoApproved`, pas de PENDING existant) des 3 contrôleurs :
- Si `createRevision()` retourne `null` → flash 'info' "Aucune modification détectée" + return
- Pas de notification, pas de sauvegarde inutile

Pour le chemin auto-approuvé : la valeur de retour est ignorée, `parent::updateEntity()` s'exécute toujours.

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Repository/PageHistoryRepository.php` | Ajout `findLatest()` |
| `src/Repository/FormationHistoryRepository.php` | Ajout `findLatest()` |
| `src/Repository/WorksHistoryRepository.php` | Ajout `findLatest()` |
| `src/Service/RevisionService.php` | `saveToTypedHistory()` retourne `bool` + dedup ; `createRevision()` retourne `?Revision` |
| `src/Controller/Admin/PageCrudController.php` | Gestion `null` dans `updateEntity()` PENDING |
| `src/Controller/Admin/FormationCrudController.php` | Gestion `null` dans `updateEntity()` PENDING |
| `src/Controller/Admin/WorksCrudController.php` | Gestion `null` dans `updateEntity()` PENDING |
