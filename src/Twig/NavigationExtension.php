<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Formation;
use App\Entity\Page;
use App\Repository\FormationRepository;
use App\Repository\PageRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Fournit les données de navigation (formations et pages publiées) à tous les templates.
 */
final class NavigationExtension extends AbstractExtension
{
    /** @var Formation[]|null */
    private ?array $formations = null;

    /** @var Page[]|null */
    private ?array $pages = null;

    public function __construct(
        private readonly FormationRepository $formationRepository,
        private readonly PageRepository $pageRepository,
    ) {
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('nav_formations', $this->getPublishedFormations(...)),
            new TwigFunction('nav_pages', $this->getPublishedPages(...)),
        ];
    }

    /**
     * Retourne les formations publiées pour le menu de navigation.
     * Le résultat est mis en cache en mémoire pour éviter plusieurs requêtes par page.
     *
     * @return Formation[]
     */
    public function getPublishedFormations(): array
    {
        if ($this->formations === null) {
            $this->formations = $this->formationRepository->findAllPublished();
        }

        return $this->formations;
    }

    /**
     * Retourne les pages publiées pour le menu de navigation.
     * Le résultat est mis en cache en mémoire pour éviter plusieurs requêtes par page.
     *
     * @return Page[]
     */
    public function getPublishedPages(): array
    {
        if ($this->pages === null) {
            $this->pages = $this->pageRepository->findAllPublished();
        }

        return $this->pages;
    }
}
