<?php

declare(strict_types=1);

namespace App\Service\Layout;

use App\Entity\User;
use App\Twig\NavigationExtension;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Construit les props (tableaux sérialisables) des îlots React de mise en page :
 * en-tête, pied de page et hero de l'accueil.
 *
 * Toute la logique d'accès reste ici, côté serveur : un lien n'est présent dans
 * les props que si l'utilisateur a le droit de le voir. Les composants React se
 * contentent d'afficher ce qu'ils reçoivent, sans jamais tester de rôle.
 * Voir docs/architecture/decision-ilots-react.md.
 */
final class LayoutPropsBuilder
{
    public function __construct(
        private readonly NavigationExtension $navigation,
        private readonly Security $security,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly Packages $packages,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function header(): array
    {
        $formations = [];
        foreach ($this->navigation->getPublishedFormations() as $formation) {
            $formations[] = [
                'label' => $formation->getTitle(),
                'url' => $this->url('app_formation_show', ['slug' => $formation->getSlug()]),
                'recruiting' => 'recruiting' === $formation->getStatus(),
            ];
        }

        return [
            'homeUrl' => $this->url('app_home'),
            'logo' => $this->logo(),
            'tagline' => 'Centre de formation 2 mille asbl',
            // « match » : préfixe d'URL qui rend l'entrée active (calculé côté client à chaque navigation Turbo)
            'nav' => [
                ['type' => 'link', 'label' => 'Accueil', 'url' => $this->url('app_home'), 'match' => '/', 'exact' => true],
                [
                    'type' => 'dropdown',
                    'label' => 'Nos formations',
                    'match' => '/formation',
                    'items' => $formations,
                    'emptyLabel' => 'Aucune formation disponible',
                    'footerLink' => ['label' => 'Voir toutes les formations →', 'url' => $this->url('app_home') . '#formations']
                ],
                [
                    'type' => 'dropdown',
                    'label' => 'Nos activités',
                    'match' => '/activites',
                    'items' => $this->activities(),
                    'emptyLabel' => 'Aucune activité disponible',
                    'external' => ['label' => 'CF2d', 'url' => 'https://cf2d.be/', 'title' => 'Visiter le site CF2d (nouvel onglet)']
                ],
                ['type' => 'link', 'label' => 'Nous contacter', 'url' => $this->url('app_contact'), 'match' => '/contact', 'exact' => false],
            ],
            'user' => $this->user(),
            'loginUrl' => $this->url('app_login'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function footer(): array
    {
        return [
            'homeUrl' => $this->url('app_home'),
            'logo' => $this->logo(),
            'tagline' => 'Centre de Formation 2M',
            'activities' => $this->activities(),
            'year' => (int) date('Y'),
        ];
    }

    /**
     * Contenu du hero de l'accueil (maquette Figma « Header + Hero »).
     *
     * @return array<string, mixed>
     */
    public function hero(): array
    {
        return [
            'title' => 'Formations professionnelles',
            'subtitle' => 'des métiers du numérique',
            'stats' => [
                ['value' => '100%', 'label' => 'gratuit'],
                ['value' => '80%', 'label' => 'de pratique'],
            ],
            'tagline' => "Change d'orientation",
            'cta' => ['label' => 'Nos formations', 'url' => '#formations'],
            'background' => $this->heroBackground(),
            'portrait' => [
                'src' => $this->packages->getUrl('images/hero-portrait.webp'),
                'alt' => 'Stagiaire CF2m',
                'ring' => $this->packages->getUrl('images/cercle-contour-blanc.svg'),
            ],
        ];
    }

    /**
     * Image de fond du hero, utilisée aussi pour le préchargement (LCP) dans le <head>.
     *
     * @return array{desktop: string, mobile: string}
     */
    public function heroBackground(): array
    {
        return [
            'desktop' => $this->packages->getUrl('images/hero-bg.webp'),
            'mobile' => $this->packages->getUrl('images/hero-bg-mobile.webp'),
        ];
    }

    /**
     * @return array{name: string, profileUrl: string, adminUrl: ?string, pendingRevisions: ?int, logoutUrl: string}|null
     */
    private function user(): ?array
    {
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return null;
        }

        $user = $this->security->getUser();
        $name = $user instanceof User ? ($user->getUserName() ?? $user->getEmail()) : $user?->getUserIdentifier();

        $canAdmin = $this->security->isGranted('ROLE_STAGIAIRE');
        $pending = null;
        if ($canAdmin && $this->security->isGranted('ROLE_ADMIN')) {
            $count = $this->navigation->getPendingRevisionsCount();
            $pending = $count > 0 ? $count : null;
        }

        return [
            'name' => (string) $name,
            'profileUrl' => $this->url('app_profile'),
            'adminUrl' => $canAdmin ? '/admin' : null,
            'pendingRevisions' => $pending,
            'logoutUrl' => $this->url('app_logout'),
        ];
    }

    /**
     * @return list<array{label: ?string, url: string}>
     */
    private function activities(): array
    {
        $activities = [];
        foreach ($this->navigation->getPublishedPages() as $page) {
            $activities[] = [
                'label' => $page->getTitle(),
                'url' => $this->url('app_page_show', ['slug' => $page->getSlug()]),
            ];
        }

        return $activities;
    }

    /**
     * @return array{dark: string, light: string}
     */
    private function logo(): array
    {
        return [
            'dark' => $this->packages->getUrl('images/logo-cf2m-blanc.svg'),
            'light' => $this->packages->getUrl('images/logo-cf2m-bleu.svg'),
        ];
    }

    /**
     * @param array<string, mixed> $parameters
     */
    private function url(string $route, array $parameters = []): string
    {
        return $this->urlGenerator->generate($route, $parameters);
    }
}
