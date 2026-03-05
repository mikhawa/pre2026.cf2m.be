<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Works;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Works>
 */
final class WorksFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Works::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return function (): array {
            $title = self::faker()->unique()->sentence(4);
            $title = rtrim($title, '.');

            return [
                'title'       => $title,
                'slug'        => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '', '-')),
                'description' => self::faker()->paragraphs(2, true),
                'status'      => self::faker()->randomElement(['draft', 'published', 'archived']),
                'publishedAt' => self::faker()->boolean(60)
                    ? \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now'))
                    : null,
                'formation'   => FormationFactory::new(),
            ];
        };
    }

    /**
     * État : travail publié
     */
    public function publie(): static
    {
        return $this->with([
            'status'      => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months', 'now')),
        ]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
