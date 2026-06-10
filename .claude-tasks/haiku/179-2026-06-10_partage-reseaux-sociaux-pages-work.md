# 179 — Ajout liens de partage réseaux sociaux sur les pages work

**Modèle** : Haiku
**Justification** : Ajout HTML/CSS simple, aucune logique métier

## Fichiers modifiés
- `templates/works/show.html.twig` — bloc "Partager" dans la sidebar
- `assets/styles/app.css` — styles `.cf2m-work-share-*`

## Résumé
Ajout de 4 boutons de partage (Facebook, X/Twitter, LinkedIn, WhatsApp) dans la
sidebar des pages de détail d'une réalisation étudiante (`/formation/{slug}/works/{slug}`).
Les boutons sont **exclusifs à ce template**, ils n'apparaissent nulle part ailleurs.
L'URL partagée est générée avec la fonction Twig `url()` (URL absolue).

Ajout des balises **Open Graph** et **Twitter Card** dans un bloc `{% block meta_og %}`
(vide par défaut dans base.html.twig, rempli uniquement sur show.html.twig).
Image OG : logo de la formation si disponible, sinon `hero-groupe.jpg` en fallback.
Ces balises permettent aux réseaux sociaux d'afficher un aperçu riche (titre, description, image).
