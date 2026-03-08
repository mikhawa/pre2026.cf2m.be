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
    private const TITRES = [
        'À propos du centre',
        'Nos formations',
        'Modalités d\'inscription',
        'Vie étudiante',
        'Nos partenaires',
        'Débouchés professionnels',
        'Équipe pédagogique',
        'Locaux et infrastructures',
        'Calendrier des portes ouvertes',
        'Aides et financement',
        'Témoignages d\'anciens étudiants',
        'Règlement intérieur',
        'Politique de confidentialité',
        'Accessibilité',
        'Plan d\'accès',
    ];

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
            $title = self::faker()->randomElement(self::TITRES);
            $slug  = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '', '-'))
                . '-' . self::faker()->unique()->numberBetween(1, 99999);

            return [
                'title'       => $title,
                'slug'        => $slug,
                'content'     => '<p>' . self::faker()->realText(300) . '</p><p>' . self::faker()->realText(300) . '</p><p>' . self::faker()->realText(200) . '</p>',
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
