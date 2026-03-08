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
    private const TITRES = [
        'Portfolio photographique personnel',
        'Application mobile de réservation',
        'Identité visuelle pour une startup',
        'Clip promotionnel pour association',
        'Site vitrine e-commerce artisanal',
        'Affiche de festival musical',
        'Interface de tableau de bord analytique',
        'Série de photos documentaires urbaines',
        'Jingle et habillage sonore radio',
        'Maquette d\'application de livraison',
        'Reportage vidéo d\'entreprise',
        'Création de logo et charte graphique',
        'Refonte de site institutionnel',
        'Campagne publicitaire pour ONG',
        'Modélisation 3D d\'un produit industriel',
        'Podcast éducatif sur l\'histoire locale',
        'Newsletter mensuelle illustrée',
        'Tutoriel vidéo de formation en ligne',
        'Illustration éditoriale pour magazine',
        'Application web de gestion d\'événements',
    ];

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
            $title = self::faker()->randomElement(self::TITRES);
            $slug  = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $title) ?? '', '-'))
                . '-' . self::faker()->unique()->numberBetween(1, 99999);

            return [
                'title'       => $title,
                'slug'        => $slug,
                'description' => self::faker()->realText(400),
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
