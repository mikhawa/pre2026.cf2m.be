<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\FormationResource;
use App\Entity\Formation;
use App\Repository\FormationRepository;
use Symfony\Component\Asset\Packages;

/**
 * Fournit les formations visibles sur le site (publiées ou en recrutement).
 * Une formation en brouillon n'est jamais renvoyée : le détail répond alors 404.
 *
 * @implements ProviderInterface<FormationResource>
 */
final class FormationProvider implements ProviderInterface
{
    private const STATUTS_VISIBLES = ['published', 'recruiting'];

    public function __construct(
        private readonly FormationRepository $formationRepository,
        private readonly Packages $packages,
    ) {
    }

    /**
     * @return FormationResource[]|FormationResource|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|FormationResource|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return array_map($this->versRessource(...), $this->formationRepository->findAllPublished());
        }

        $formation = $this->formationRepository->findOneBySlug((string) ($uriVariables['slug'] ?? ''));

        if (null === $formation || !\in_array($formation->getStatus(), self::STATUTS_VISIBLES, true)) {
            return null;
        }

        return $this->versRessource($formation);
    }

    private function versRessource(Formation $formation): FormationResource
    {
        $logo = $formation->getLogo();

        return new FormationResource(
            slug: (string) $formation->getSlug(),
            titre: (string) $formation->getTitle(),
            descriptionCourte: $formation->getDescriptionCourte(),
            statut: $formation->getStatus(),
            logoUrl: $logo ? $this->packages->getUrl('uploads/formation-logos/'.$logo) : null,
            couleurPrincipale: $formation->getColorPrimary(),
            couleurSecondaire: $formation->getColorSecondary(),
            publieLe: $formation->getPublishedAt(),
        );
    }
}
