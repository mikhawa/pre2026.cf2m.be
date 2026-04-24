---
modèle: haiku
justification: Ajout de balises HTML statiques, aucune logique métier
fichiers:
  - templates/base.html.twig
  - templates/home/index.html.twig
  - templates/formation/show.html.twig
  - templates/page/show.html.twig
  - templates/works/show.html.twig
  - templates/contact/index.html.twig
  - public/robots.txt (création)
résumé: Ajout du bloc meta_description dans base.html.twig + override par page + creation robots.txt
résultat: ✅
---

## Travail effectué

### 1. base.html.twig
- Ajout des blocs `meta_description` et `meta_robots` après la balise `<title>`
- Descriptions par défaut à l'index racine du site
- Permet à chaque page enfant de surcharger via `{% block meta_description %}`

### 2. home/index.html.twig
- Surcharge `meta_description` avec description adaptée à l'accueil
- Formulations ciblées : formations professionnelles, informatique, multimédia, digital, Belgique

### 3. formation/show.html.twig
- Surcharge avec `formation.descriptionCourte` ou extraction dynamique des 155 premiers caractères de la description
- Échappement XSS via `|e('html_attr')`

### 4. page/show.html.twig
- Surcharge avec contenu extrait du champ `page.content`
- Suppression des balises HTML via `|striptags`
- 155 caractères max + ellipse

### 5. works/show.html.twig
- Surcharge avec contenu du champ `work.description`
- Même pattern que page/show.html.twig

### 6. contact/index.html.twig
- Surcharge avec description adaptée à la page de contact
- Focus sur demandes d'information et inscriptions

### 7. public/robots.txt
- Création du fichier robots.txt à la racine du projet public/
- Accepte tous les robots (`User-agent: *`, `Allow: /`)
- Interdit : `/admin/`, `/profil/modifier`, `/reinitialiser-mot-de-passe/`, `/connexion`, `/inscription`

## Résultat
Tous les fichiers modifiés comme demandé. Aucune autre modification apportée.
