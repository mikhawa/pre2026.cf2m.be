<?php

declare(strict_types=1);

namespace App\Tests\State;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\FormationResource;
use App\Entity\Formation;
use App\Repository\FormationRepository;
use App\State\FormationProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;

/**
 * L'API ne doit exposer que les formations visibles sur le site, et uniquement leurs champs publics.
 */
class FormationProviderTest extends TestCase
{
    private function formation(string $slug, string $status, ?string $logo = null): Formation
    {
        $formation = new Formation();
        $formation->setTitle('Développeur web');
        $formation->setSlug($slug);
        $formation->setStatus($status);
        $formation->setDescriptionCourte('Formation de 9 mois');
        $formation->setLogo($logo);

        return $formation;
    }

    private function provider(FormationRepository $repository): FormationProvider
    {
        $packages = $this->createStub(Packages::class);
        $packages->method('getUrl')->willReturnCallback(static fn (string $path): string => '/'.$path);

        return new FormationProvider($repository, $packages);
    }

    public function testLaListeRenvoieLesFormationsPubliees(): void
    {
        $repository = $this->createMock(FormationRepository::class);
        $repository->expects(self::once())->method('findAllPublished')
            ->willReturn([$this->formation('dev-web', 'recruiting', 'logo.png')]);

        $resultat = $this->provider($repository)->provide(new GetCollection());

        self::assertIsArray($resultat);
        self::assertCount(1, $resultat);
        self::assertInstanceOf(FormationResource::class, $resultat[0]);
        self::assertSame('dev-web', $resultat[0]->slug);
        self::assertSame('Développeur web', $resultat[0]->titre);
        self::assertSame('recruiting', $resultat[0]->statut);
        self::assertSame('/uploads/formation-logos/logo.png', $resultat[0]->logoUrl);
    }

    public function testLeDetailRenvoieUneFormationPubliee(): void
    {
        $repository = $this->createStub(FormationRepository::class);
        $repository->method('findOneBySlug')->willReturn($this->formation('dev-web', 'published'));

        $resultat = $this->provider($repository)->provide(new Get(), ['slug' => 'dev-web']);

        self::assertInstanceOf(FormationResource::class, $resultat);
        self::assertNull($resultat->logoUrl);
    }

    public function testUneFormationEnBrouillonEstIntrouvable(): void
    {
        $repository = $this->createStub(FormationRepository::class);
        $repository->method('findOneBySlug')->willReturn($this->formation('secret', 'draft'));

        self::assertNull($this->provider($repository)->provide(new Get(), ['slug' => 'secret']));
    }

    public function testUnSlugInconnuEstIntrouvable(): void
    {
        $repository = $this->createStub(FormationRepository::class);
        $repository->method('findOneBySlug')->willReturn(null);

        self::assertNull($this->provider($repository)->provide(new Get(), ['slug' => 'inconnu']));
    }
}
