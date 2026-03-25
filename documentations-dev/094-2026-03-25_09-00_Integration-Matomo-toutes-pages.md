# 094 — Intégration Matomo sur toutes les pages (frontend + EasyAdmin)

**Date** : 2026-03-25 09:00
**Modèle** : Haiku

---

## Contexte

Le site CF2m nécessite un suivi statistique des visites sur l'ensemble de ses pages, y compris le back-office EasyAdmin, afin de mesurer la fréquentation réelle de la plateforme. L'instance Matomo CF2m est hébergée sur `statistiques.cf2m.be`.

---

## Fichiers modifiés/créés

| Fichier | Action | Rôle |
|---------|--------|------|
| `templates/_matomo.html.twig` | Créé | Partial réutilisable contenant le code de suivi |
| `templates/base.html.twig` | Modifié | Include du partial avant `</body>` |
| `templates/bundles/EasyAdminBundle/layout.html.twig` | Créé | Override du layout EasyAdmin, inject Matomo |

---

## Architecture mise en place

### Partial unique `_matomo.html.twig`

Le code de suivi est centralisé dans un seul fichier pour éviter la duplication et faciliter les futures mises à jour (changement de site ID, de domaine, etc.) :

```twig
{# templates/_matomo.html.twig #}
<script>
  var _paq = window._paq = window._paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//statistiques.cf2m.be/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '3']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
```

### Intégration frontend (`base.html.twig`)

Toutes les pages frontend héritant de `base.html.twig` reçoivent le tracking via un include avant la fermeture du `</body>` :

```twig
{# juste avant </body> #}
{% include '_matomo.html.twig' %}
```

Cela couvre : accueil, formations, pages d'activités, contact, profil, connexion, réinitialisation de mot de passe.

### Intégration EasyAdmin (`templates/bundles/EasyAdminBundle/layout.html.twig`)

EasyAdmin possède son propre layout qui n'hérite pas de `base.html.twig`. L'override Symfony permet d'injecter le partial dans le block `body_javascript` d'EasyAdmin :

```twig
{% extends '@!EasyAdmin/layout.html.twig' %}

{% block body_javascript %}
    {{ parent() }}
    {% include '_matomo.html.twig' %}
{% endblock %}
```

> **Note** : La syntaxe `@!EasyAdmin/layout.html.twig` (avec `!`) est obligatoire pour étendre le template du bundle tout en le surchargeant via `templates/bundles/`. Sans le `!`, Twig crée une récursion infinie.

---

## Paramètres Matomo

| Paramètre | Valeur |
|-----------|--------|
| Instance | `statistiques.cf2m.be` |
| Site ID | `3` |
| Méthodes activées | `trackPageView`, `enableLinkTracking` |

---

## Raison

Demande client : avoir des statistiques de fréquentation complètes sur l'ensemble du site, y compris l'interface d'administration, via l'instance Matomo auto-hébergée du CF2m.
