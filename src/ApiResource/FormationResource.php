<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\State\FormationProvider;

/**
 * Formation telle qu'exposée publiquement par l'API (lecture seule).
 * DTO volontairement séparé de l'entité App\Entity\Formation : seuls les champs
 * publics sortent (pas de créateur, stagiaires, inscriptions…).
 */
#[ApiResource(
    shortName: 'Formation',
    operations: [
        // Peu de formations : liste complète, sans pagination
        new GetCollection(paginationEnabled: false),
        new Get(),
    ],
    provider: FormationProvider::class,
)]
final class FormationResource
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public readonly string $slug,
        public readonly string $titre,
        public readonly ?string $descriptionCourte,
        /** « published » ou « recruiting » */
        public readonly string $statut,
        public readonly ?string $logoUrl,
        public readonly ?string $couleurPrincipale,
        public readonly ?string $couleurSecondaire,
        public readonly ?\DateTimeImmutable $publieLe,
    ) {
    }
}
