<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Fournit les données de navigation (formations publiées) à tous les templates.
 */
final class NavigationExtension extends AbstractExtension
{
    /** @var Formation[]|null */
    private ?array $formations = null;

    public function __construct(private readonly FormationRepository $formationRepository)
    {
    }

    /** @return TwigFunction[] */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('nav_formations', $this->getPublishedFormations(...)),
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
}
