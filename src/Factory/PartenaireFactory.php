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
            'nom'         => self::faker()->company(),
            'description' => self::faker()->paragraph(),
            'logo'   => null,
            'url'    => self::faker()->optional(0.8)->url(),
            'active' => true,
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
