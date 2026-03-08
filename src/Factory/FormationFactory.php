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
            $title = self::faker()->randomElement(self::TITRES);
            $slug  = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '', '-'))
                . '-' . self::faker()->unique()->numberBetween(1, 99999);

            return [
                'title'       => $title,
                'slug'        => $slug,
                'description' => self::faker()->realText(600),
                'createdAt'   => \DateTimeImmutable::createFromMutable(
                    self::faker()->dateTimeBetween('-3 years', '-1 year')
                ),
                'status'      => self::faker()->randomElement(['draft', 'published', 'archived']),
                'publishedAt' => null,
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
        $createdAt = \DateTimeImmutable::createFromMutable(
            self::faker()->dateTimeBetween('-3 years', '-1 year')
        );

        return $this->with([
            'createdAt'   => $createdAt,
            'status'      => 'published',
            'publishedAt' => \DateTimeImmutable::createFromMutable(
                self::faker()->dateTimeBetween('-11 months', '-3 months')
            ),
        ]);
    }

    /**
     * État : formation en brouillon
     */
    public function brouillon(): static
    {
        return $this->with([
            'createdAt'   => \DateTimeImmutable::createFromMutable(
                self::faker()->dateTimeBetween('-3 years', '-1 year')
            ),
            'status'      => 'draft',
            'publishedAt' => null,
        ]);
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
