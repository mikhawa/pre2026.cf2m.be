<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Rating;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Rating>
 */
final class RatingFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Rating::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'value' => self::faker()->numberBetween(1, 5),
            'user'  => UserFactory::new(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
