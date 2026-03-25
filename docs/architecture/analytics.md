# Analytics — Matomo

## Instance

| Paramètre | Valeur |
|-----------|--------|
| URL | `https://statistiques.cf2m.be` |
| Site ID | `3` |
| Hébergement | Auto-hébergé sur VPS CF2m |

---

## Intégration Twig

Le code de suivi est centralisé dans un partial unique :

```
templates/
├── _matomo.html.twig                  # Partial réutilisable (source unique)
├── base.html.twig                     # Frontend — include avant </body>
└── bundles/EasyAdminBundle/
    └── layout.html.twig               # Back-office — override block body_javascript
```

### Partial `_matomo.html.twig`

Source unique du script Matomo. Pour modifier les paramètres (site ID, domaine), **ne modifier que ce fichier**.

### Frontend

`base.html.twig` inclut le partial juste avant `</body>`. Toutes les pages héritant de ce layout sont couvertes : accueil, formations, activités, contact, profil, connexion.

### EasyAdmin

`templates/bundles/EasyAdminBundle/layout.html.twig` étend `@!EasyAdmin/layout.html.twig` (syntaxe `!` obligatoire pour éviter la récursion) et surcharge le block `body_javascript` en appelant `{{ parent() }}` puis en incluant le partial.

---

## Modifier les paramètres

Pour changer le site ID ou l'URL Matomo, éditer uniquement :

```
templates/_matomo.html.twig
```

---

## Pages couvertes

| Zone | Mécanisme |
|------|-----------|
| Toutes les pages publiques | `base.html.twig` |
| Back-office EasyAdmin | Override `bundles/EasyAdminBundle/layout.html.twig` |
| Page de connexion (`/login`) | Hérite de `base.html.twig` ✓ |
| Réinitialisation de mot de passe | Hérite de `base.html.twig` ✓ |
