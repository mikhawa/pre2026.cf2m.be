<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Page;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Page>
 */
final class PageFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Page::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return function (): array {
            $title = self::faker()->unique()->words(3, true);
            $title = ucfirst($title);

            return [
                'title'       => $title,
                'slug'        => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '', '-')),
                'content'     => '<p>' . implode('</p><p>', self::faker()->paragraphs(3)) . '</p>',
                'status'      => self::faker()->randomElement(['draft', 'published']),
                'publishedAt' => self::faker()->boolean(70)
                    ? \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now'))
                    : null,
            ];
        };
    }

    /**
     * État : page publiée
     */
    public function publiee(): static
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
