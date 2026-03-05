<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Inscription;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Inscription>
 */
final class InscriptionFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Inscription::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'nom'       => self::faker()->lastName(),
            'prenom'    => self::faker()->firstName(),
            'email'     => self::faker()->safeEmail(),
            'message'   => self::faker()->optional(0.8)->paragraph(),
            'formation' => FormationFactory::new(),
            'treat'     => false,
            'treatAt'   => null,
        ];
    }

    /**
     * État : inscription traitée
     */
    public function traitee(): static
    {
        return $this->with([
            'treat'   => true,
            'treatAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-3 months', 'now')),
        ]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
