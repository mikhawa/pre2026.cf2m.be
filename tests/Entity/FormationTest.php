<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\User;
use App\Entity\Works;
use PHPUnit\Framework\TestCase;

class FormationTest extends TestCase
{
    private Formation $formation;

    protected function setUp(): void
    {
        $this->formation = new Formation();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->formation->getId());
        self::assertNull($this->formation->getTitle());
        self::assertNull($this->formation->getSlug());
        self::assertNull($this->formation->getDescription());
        self::assertNull($this->formation->getDescriptionCourte());
        self::assertNull($this->formation->getLogo());
        self::assertNull($this->formation->getLogoFile());
        self::assertNull($this->formation->getCreatedAt());
        self::assertNull($this->formation->getPublishedAt());
        self::assertNull($this->formation->getUpdatedAt());
        self::assertNull($this->formation->getCreatedBy());
        self::assertNull($this->formation->getUpdatedBy());
        self::assertSame('draft', $this->formation->getStatus());
        self::assertNull($this->formation->getColorPrimary());
        self::assertNull($this->formation->getColorSecondary());
    }

    public function testCollectionsInitialized(): void
    {
        self::assertCount(0, $this->formation->getResponsables());
        self::assertCount(0, $this->formation->getWorks());
        self::assertCount(0, $this->formation->getInscriptions());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->formation, $this->formation->setTitle('Formation PHP'));
        self::assertSame($this->formation, $this->formation->setSlug('formation-php'));
        self::assertSame($this->formation, $this->formation->setDescription('Description'));
        self::assertSame($this->formation, $this->formation->setDescriptionCourte('Description courte'));
        self::assertSame($this->formation, $this->formation->setLogo('logo.png'));
        self::assertSame($this->formation, $this->formation->setStatus('published'));
        self::assertSame($this->formation, $this->formation->setPublishedAt(new \DateTimeImmutable()));
        self::assertSame($this->formation, $this->formation->setUpdatedAt(new \DateTimeImmutable()));
        self::assertSame($this->formation, $this->formation->setCreatedBy(new User()));
        self::assertSame($this->formation, $this->formation->setUpdatedBy(new User()));
        self::assertSame($this->formation, $this->formation->setColorPrimary('#1a2e4a'));
        self::assertSame($this->formation, $this->formation->setColorSecondary('#00b4d8'));
    }

    public function testDescriptionCourte(): void
    {
        self::assertNull($this->formation->getDescriptionCourte());

        $this->formation->setDescriptionCourte('Présentation rapide de la formation.');
        self::assertSame('Présentation rapide de la formation.', $this->formation->getDescriptionCourte());

        $this->formation->setDescriptionCourte(null);
        self::assertNull($this->formation->getDescriptionCourte());
    }

    public function testLogo(): void
    {
        self::assertNull($this->formation->getLogo());

        $this->formation->setLogo('logo-formation.png');
        self::assertSame('logo-formation.png', $this->formation->getLogo());

        $this->formation->setLogo(null);
        self::assertNull($this->formation->getLogo());
    }

    public function testColorFields(): void
    {
        $this->formation->setColorPrimary('#1a2e4a');
        self::assertSame('#1a2e4a', $this->formation->getColorPrimary());

        $this->formation->setColorSecondary('#00b4d8');
        self::assertSame('#00b4d8', $this->formation->getColorSecondary());

        $this->formation->setColorPrimary(null);
        self::assertNull($this->formation->getColorPrimary());

        $this->formation->setColorSecondary(null);
        self::assertNull($this->formation->getColorSecondary());
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->formation);
    }

    public function testToString(): void
    {
        $this->formation->setTitle('Formation Symfony');
        self::assertSame('Formation Symfony', (string) $this->formation);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->formation->getCreatedAt());
        $this->formation->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->formation->getCreatedAt());
    }

    public function testAddRemoveResponsable(): void
    {
        $user = new User();
        $this->formation->addResponsable($user);
        self::assertCount(1, $this->formation->getResponsables());
        self::assertCount(1, $user->getFormations());

        $this->formation->addResponsable($user);
        self::assertCount(1, $this->formation->getResponsables());

        $this->formation->removeResponsable($user);
        self::assertCount(0, $this->formation->getResponsables());
        self::assertCount(0, $user->getFormations());
    }

    public function testAddRemoveWork(): void
    {
        $works = new Works();
        $this->formation->addWork($works);
        self::assertCount(1, $this->formation->getWorks());
        self::assertSame($this->formation, $works->getFormation());

        $this->formation->addWork($works);
        self::assertCount(1, $this->formation->getWorks());

        $this->formation->removeWork($works);
        self::assertCount(0, $this->formation->getWorks());
    }

    public function testAddRemoveInscription(): void
    {
        $inscription = new Inscription();
        $this->formation->addInscription($inscription);
        self::assertCount(1, $this->formation->getInscriptions());
        self::assertSame($this->formation, $inscription->getFormation());

        $this->formation->addInscription($inscription);
        self::assertCount(1, $this->formation->getInscriptions());

        $this->formation->removeInscription($inscription);
        self::assertCount(0, $this->formation->getInscriptions());
    }

    public function testCreatedBy(): void
    {
        $user = new User();
        $this->formation->setCreatedBy($user);
        self::assertSame($user, $this->formation->getCreatedBy());
    }

    public function testUpdatedBy(): void
    {
        $user = new User();
        $this->formation->setUpdatedBy($user);
        self::assertSame($user, $this->formation->getUpdatedBy());

        $this->formation->setUpdatedBy(null);
        self::assertNull($this->formation->getUpdatedBy());
    }
}
