<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Formation;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Formation>
 */
final class FormationFactory extends PersistentObjectFactory
{
    private const TITRES = [
        'Développement web fullstack',
        'Design graphique avancé',
        'Photographie numérique',
        'Marketing digital et réseaux sociaux',
        'Animation 3D et motion design',
        'Montage vidéo professionnel',
        'Intégration HTML/CSS responsive',
        'UX/UI Design et prototypage',
        'Infographie et identité visuelle',
        'Audio et prise de son studio',
        'Illustration numérique',
        'Développement d\'applications mobiles',
        'Rédaction web et copywriting',
        'E-commerce et stratégie de vente en ligne',
        'Cybersécurité pour non-spécialistes',
        'Gestion de projet agile',
        'Production audiovisuelle',
        'Accessibilité numérique',
        'WordPress et CMS open source',
        'Photographie de studio et éclairage',
    ];

    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Formation::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return function (): array {
            $title = self::faker()->unique()->randomElement(self::TITRES);

            return [
                'title'       => $title,
                'slug'        => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '', '-')),
                'description' => self::faker()->realText(600),
                'status'      => self::faker()->randomElement(['draft', 'published', 'archived']),
                'publishedAt' => self::faker()->boolean(70)
                    ? \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now'))
                    : null,
                'createdBy'   => UserFactory::new(),
                'updatedAt'   => null,
            ];
        };
    }

    /**
     * État : formation publiée
     */
    public function publiee(): static
    {
        return $this->with([
            'status'      => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-6 months', 'now')),
        ]);
    }

    /**
     * État : formation en brouillon
     */
    public function brouillon(): static
    {
        return $this->with(['status' => 'draft', 'publishedAt' => null]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
