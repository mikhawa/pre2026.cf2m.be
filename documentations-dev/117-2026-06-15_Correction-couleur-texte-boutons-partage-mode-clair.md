# 117 — Correction : texte blanc sur boutons de partage en mode clair

**Date :** 2026-06-15  
**Commit :** `5ee852e`  
**Branche :** `feature/24-prepa-for-design`

---

## Problème

En mode clair (`data-theme="light"`), les boutons de partage réseaux sociaux de la sidebar Works (Facebook, X/Twitter, LinkedIn, WhatsApp) affichaient un texte illisible : la règle globale du mode clair écrasait le `color: #fff` défini sur `.cf2m-work-share-btn`.

**Règle fautive (ligne 1695, `app.css`) :**

```css
[data-theme="light"] a { color: #0072a3; }
```

Cette règle générale s'appliquait à tous les `<a>` en mode clair, y compris les boutons de partage, remplaçant le blanc par du bleu pétrole — illisible sur les fonds colorés des boutons.

---

## Correctif

Ajout d'un override ciblé après les règles de sidebar Works en mode clair :

```css
[data-theme="light"] .cf2m-work-share-btn,
[data-theme="light"] .cf2m-work-share-btn:hover { color: #fff !important; }
```

Le `!important` est nécessaire pour prendre le dessus sur la règle globale `[data-theme="light"] a { color: … }` qui a une spécificité équivalente.

---

## Fichier modifié

- `assets/styles/app.css`

---

## Note

Après cette correction, `php bin/console asset-map:compile` a été relancé pour régénérer le fichier compilé `public/assets/styles/app-*.css` servant les assets via nginx.
