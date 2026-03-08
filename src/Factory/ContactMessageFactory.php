<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\ContactMessage;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ContactMessage>
 */
final class ContactMessageFactory extends PersistentObjectFactory
{
    private const SUJETS = [
        'Demande d\'information sur les formations',
        'Question relative aux modalités d\'inscription',
        'Renseignements sur les frais de scolarité',
        'Demande de rendez-vous avec un conseiller',
        'Question sur les débouchés professionnels',
        'Signalement d\'un problème technique sur le site',
        'Demande de documentation sur le programme',
        'Information sur les dates de rentrée',
        'Question sur la prise en charge par un organisme',
        'Candidature spontanée pour un poste de formateur',
        'Partenariat potentiel avec notre entreprise',
        'Demande de devis pour une formation en entreprise',
        'Réclamation suite à une inscription',
        'Félicitations pour la qualité de l\'enseignement',
        'Question sur l\'accessibilité des locaux',
    ];

    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return ContactMessage::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'nom'     => self::faker()->lastName() . ' ' . self::faker()->firstName(),
            'email'   => self::faker()->safeEmail(),
            'sujet'   => self::faker()->randomElement(self::SUJETS),
            'message' => self::faker()->realText(350),
            'read'    => false,
        ];
    }

    /**
     * État : message lu
     */
    public function lu(): static
    {
        return $this->with(['read' => true]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
