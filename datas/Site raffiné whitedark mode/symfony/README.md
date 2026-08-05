# Intégration Symfony — CF2m

Livrable : templates Twig + `app.css` (tokens + classes `cf2m-`) reprenant le design validé.
Aucun build step : Bootstrap n'est plus requis pour ces pages, tout est dans un seul CSS.

## Arborescence à copier

```
assets/styles/app.css                    → remplace l'ancien (66 Ko → ~14 Ko)
assets/controllers/theme_controller.js   → toggle dark/light + direction visuelle
assets/controllers/burger_controller.js  → menu mobile (<1060px)
templates/base.html.twig
templates/_partials/navbar.html.twig
templates/_partials/footer.html.twig
templates/_partials/footer_slim.html.twig
templates/_partials/formation_card.html.twig
templates/home/index.html.twig
templates/formation/show.html.twig
templates/contact/index.html.twig
templates/security/login.html.twig
```

Images attendues dans `public/images/` : `logo-cf2m-blanc.svg`, `logo-cf2m-bleu.svg`,
`cercle-contour-bleuclair.svg`, `hero-bg.jpg`, `hero-portrait.jpg`,
`formation-icons/*.svg` (un fichier par formation, cf. `Formation::icon`).

## Thème & direction visuelle

- `<html data-theme="dark|light" data-dir="a|b">`.
- Le script inline de `base.html.twig` relit `localStorage` avant le premier paint (pas de flash).
- `theme_controller.js` écrit `cf2m-theme` et `cf2m-dir`.
- **Direction A** (défaut) : display 800, coins 20 px, photo de fond fixe, verre dépoli.
  **Direction B** : display 500, coins 3 px, fond plat, filets or, plus dense.
  Le bouton « Direction A / B » de la navbar sert à comparer — à supprimer une fois votre choix fait,
  puis figez `data-dir` dans `base.html.twig`.

## Données attendues par les templates

### Entité `Formation`
| Propriété | Type | Utilisée par |
|---|---|---|
| `name` | string | carte, fiche, nav |
| `slug` | string | routes |
| `shortDescription` | string | carte accueil |
| `lead` | text | chapô fiche |
| `icon` | string (nom de fichier SVG) | carte |
| `statusLabel` | string ("Recrutement", "Complet", "Continu") | badge |
| `open` | bool | style du badge (plein / discret) |
| `recruiting` | bool | badge dans le menu déroulant |
| `duration` | string ("12 mois") | méta fiche |
| `nextSession` | string ("Sept. 2026") | méta fiche |
| `price` | string \| null | méta fiche (défaut « Gratuit ») |
| `groupSize` | int | méta fiche |
| `modules` | Collection<FormationModule> | programme (`name`, `description`, ordonné) |

### Autres
- `Partner` : `name`, `url` (nullable) → section partenaires.
- `stats` : itérable de `{value, label}` — entité `Stat` ou tableau en dur dans le contrôleur.
- `ContactMessage` + `ContactType` (champs `firstName`, `lastName`, `email`, `formation`
  (EntityType sur Formation, `placeholder: 'Renseignements généraux'`), `message`).

## Contrôleurs — squelettes

```php
#[Route('/', name: 'app_home')]
public function home(FormationRepository $formations, PartnerRepository $partners): Response
{
    return $this->render('home/index.html.twig', [
        'formations' => $formations->findPublished(),
        'partners'   => $partners->findBy([], ['position' => 'ASC']),
        'stats'      => [
            ['value' => '85%',  'label' => "Taux d'insertion"],
            ['value' => '20+',  'label' => 'Années'],
            ['value' => '500+', 'label' => 'Diplômés'],
            ['value' => '100%', 'label' => 'Pratique'],
        ],
    ]);
}

#[Route('/formations/{slug}', name: 'app_formation_show')]
public function show(Formation $formation): Response
{
    return $this->render('formation/show.html.twig', ['formation' => $formation]);
}

#[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
public function contact(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
{
    $message = new ContactMessage();
    $form = $this->createForm(ContactType::class, $message);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($message);
        $em->flush();
        $mailer->send((new TemplatedEmail())
            ->to('info@cf2m.be')
            ->replyTo($message->getEmail())
            ->subject('Nouveau message — '.$message->getFullName())
            ->htmlTemplate('emails/contact.html.twig')
            ->context(['message' => $message]));

        $this->addFlash('contact_success', 'Nous vous répondrons dans les 24h ouvrables.');

        return $this->redirectToRoute('app_contact');
    }

    return $this->render('contact/index.html.twig', ['form' => $form]);
}
```

Le lien « Réserver ma place » de la fiche passe `?formation=<slug>` : pré-remplissez le champ
dans le contrôleur contact (`$message->setFormation($repo->findOneBy(['slug' => $request->query->get('formation')]))`).

## Navbar : formations du menu déroulant

Le partial lit `formations_nav`. Le plus simple, un listener Twig global :

```yaml
# config/packages/twig.yaml
twig:
    globals:
        formations_nav: '@App\Twig\FormationNavProvider'
```

…ou exposez un service appelé dans le partial. Sans valeur, le menu se rend vide sans casser la page.

## Routes référencées
`app_home`, `app_formation_show`, `app_contact`, `app_pixel_and_co`, `app_login`, `app_logout`, `app_dashboard`.
Adaptez les noms si les vôtres diffèrent (recherche/remplacement dans `templates/`).

## Sécurité (login)
`login.html.twig` suit le form_login standard : champs `_username` / `_password`, jeton CSRF
`authenticate`, variables `error` et `last_username` fournies par `AuthenticationUtils`.

## Accessibilité & performance
- `prefers-reduced-motion` neutralise animations et transitions.
- Contrastes vérifiés en clair comme en sombre (accent `#00728e` en clair, `#3cc8e6` en sombre).
- Aucune police locale : Google Fonts en `@import` — passez-la en `<link rel="preload">` dans
  `base.html.twig` si le LCP compte, ou auto-hébergez Outfit + DM Sans.
