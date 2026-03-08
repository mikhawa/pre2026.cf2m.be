<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Partenaire;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Partenaire>
 */
final class PartenaireFactory extends PersistentObjectFactory
{
    private const NOMS = [
        'Agence Pixel & Co',
        'Studio Lumière Production',
        'WebCraft Agency',
        'Fondation Numérique Wallonie',
        'Créations Grafik Pro',
        'Imprimerie Dumont & Fils',
        'Société Belge du Multimédia',
        'Atelier Son & Image',
        'Digital Campus Bruxelles',
        'Réseau Entreprises Créatives',
        'Forem Wallonie',
        'ACTIRIS Formation',
        'Fédération des Industries Créatives',
        'Vivalia Communication',
        'MediaLab Liège',
    ];

    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Partenaire::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'nom'         => self::faker()->unique()->randomElement(self::NOMS),
            'description' => self::faker()->realText(180),
            'logo'        => null,
            'url'         => self::faker()->optional(0.8)->url(),
            'active'      => true,
        ];
    }

    /**
     * État : partenaire inactif
     */
    public function inactif(): static
    {
        return $this->with(['active' => false]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
