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
            'sujet'   => self::faker()->sentence(6),
            'message' => self::faker()->paragraphs(2, true),
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
