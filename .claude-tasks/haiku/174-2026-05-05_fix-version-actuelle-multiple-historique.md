---
modèle: haiku
date: 2026-05-05
justification: Correction d'une logique de comparaison dans trois contrôleurs — bug d'affichage
---

## Bug corrigé

Plusieurs versions affichées simultanément comme "Version actuelle" dans les historiques Formation, Page et Works.

## Cause

`$isCurrent = ($snapshots[$i] === $liveSnapshot)` compare le **contenu**. Si plusieurs sauvegardes consécutives ne changent rien (v7→v8→v9→v10 avec le même contenu), elles ont toutes le même snapshot que la page live → toutes marquées "actuelle".

## Fix

Ajout d'un flag `$currentFound` avant la boucle : seule la **première correspondance** (la plus récente, car les entrées sont triées DESC) est marquée comme version actuelle.

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

## Fichiers modifiés

| Fichier | Action |
|---|---|
| `src/Controller/Admin/PageCrudController.php` | Flag `$currentFound` dans `historiquePage()` |
| `src/Controller/Admin/FormationCrudController.php` | Flag `$currentFound` dans `historiqueFormation()` |
| `src/Controller/Admin/WorksCrudController.php` | Flag `$currentFound` dans `historiqueWorks()` |
