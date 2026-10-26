# 204 — En-tête, pied de page et hero en îlots React (maquette Figma)

**Modèle utilisé** : Opus

**Justification** : composants de mise en page présents sur toutes les pages, avec données
d'authentification et de rôles (sécurité), interaction avec Turbo (éléments permanents) et SEO.

## Contexte

Première intégration de la maquette Figma (capture « Header + Hero »). L'utilisateur a choisi
des îlots React pour l'en-tête, le pied de page et le hero, plutôt que des Twig Components, en
connaissant les contreparties (SEO, remontage Turbo, données d'auth en props) — chacune est
compensée ci-dessous.

## Fichiers modifiés

- `src/Service/Layout/LayoutPropsBuilder.php` (nouveau) — props sérialisables ; liens filtrés selon les rôles côté serveur
- `src/Twig/LayoutExtension.php` (nouveau) — `header_props()`, `footer_props()`, `hero_props()`, `hero_background()`
- `assets/react/src/islands/Layout/Header.jsx`, `Layout/Footer.jsx`, `Home/Hero.jsx` (nouveaux)
- `assets/react/src/components/{Icon,Logo,ThemeToggle,NavDropdown,UserMenu}.jsx`, `hooks/useCurrentPath.jsx` (nouveaux)
- Supprimés : `islands/Demo/HelloIsland.jsx`, `components/Counter.jsx` (ux-react charge tous les îlots sur chaque page)
- `assets/react/build/**` — JSX recompilé
- `templates/base.html.twig` — en-tête et pied de page remplacés par les îlots (`data-turbo-permanent`, `permanent: true`)
- `templates/home/index.html.twig` — hero remplacé par l'îlot `Home/Hero`
- `templates/layout/_header_fallback.html.twig`, `templates/home/_hero_fallback.html.twig` (nouveaux) — contenus de repli
- `assets/styles/tokens.css`, `assets/styles/layout.css` (nouveaux), `assets/app.js` (imports)
- `tests/Service/Layout/LayoutPropsBuilderTest.php` (nouveau, 8 tests)
- `docs/architecture/frontend.md`, `docs/architecture/decision-ilots-react.md`, `config/packages/react.yaml` (exemples)

## Résumé

- **Sécurité** : React ne teste aucun rôle ; `LayoutPropsBuilder` n'envoie le lien Administration
  qu'aux `ROLE_STAGIAIRE` et le badge des révisions qu'aux `ROLE_ADMIN` (même logique que l'ancien Twig).
- **Turbo** : en-tête et pied de page permanents (montés une fois par chargement complet) ; lien
  actif recalculé à chaque `turbo:load` ; menu mobile refermé à chaque navigation. Tous les formulaires
  qui changent l'utilisateur connecté avaient déjà `data-turbo="false"` (vérifié).
- **SEO** : contenus de repli Twig (logo, liens principaux, `<h1>`, accroche, bouton) dans le HTML.
- **Design** : tokens Figma relevés à l'œil (cyan `#3fdfee`, police Outfit) — à remplacer par les valeurs
  Dev Mode. En thème clair, liens de l'en-tête en `#0d5c7a` sur les pages intérieures (cyan illisible sur blanc).
  Ajouts par rapport à la maquette, pour ne pas perdre de fonctionnalité : bouton de thème, Connexion /
  menu utilisateur ; le lien CF2d est déplacé dans « Nos activités ».
- L'en-tête n'est plus « sticky » (la maquette ne le prévoit pas).

## Résultat

- PHPUnit : OK (185 tests, 420 assertions) ; php-cs-fixer : 0 correction ; `lint:twig` : OK.
- Captures Chrome headless : accueil desktop (conforme à la maquette, hors images provisoires),
  mobile 520 px (bouton thème + menu burger), page contact en thème clair et sombre (lien actif souligné).
- Non vérifié ici : ouverture des menus déroulants et du menu mobile au clic, navigation Turbo
  aller-retour, menu utilisateur connecté (stagiaire / admin) — à tester dans le navigateur.
- Restent à fournir : images Figma (fond « mains sur clavier », portrait), valeurs Dev Mode, maquette du pied de page.
