<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Partenaire;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class PartenaireTest extends TestCase
{
    private Partenaire $partenaire;

    protected function setUp(): void
    {
        $this->partenaire = new Partenaire();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->partenaire->getId());
        self::assertNull($this->partenaire->getNom());
        self::assertNull($this->partenaire->getDescription());
        self::assertNull($this->partenaire->getLogo());
        self::assertNull($this->partenaire->getLogoFile());
        self::assertNull($this->partenaire->getUrl());
        self::assertFalse($this->partenaire->isActive());
        self::assertNull($this->partenaire->getUpdatedAt());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->partenaire, $this->partenaire->setNom('FOREM'));
        self::assertSame($this->partenaire, $this->partenaire->setDescription('Organisme public'));
        self::assertSame($this->partenaire, $this->partenaire->setLogo('forem.png'));
        self::assertSame($this->partenaire, $this->partenaire->setUrl('https://forem.be'));
        self::assertSame($this->partenaire, $this->partenaire->setActive(true));
        self::assertSame($this->partenaire, $this->partenaire->setLogoFile(null));
        self::assertSame($this->partenaire, $this->partenaire->setUpdatedAt(null));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->partenaire);
    }

    public function testToString(): void
    {
        $this->partenaire->setNom('FOREM');
        self::assertSame('FOREM', (string) $this->partenaire);
    }

    public function testIsActiveToggle(): void
    {
        self::assertFalse($this->partenaire->isActive());
        $this->partenaire->setActive(true);
        self::assertTrue($this->partenaire->isActive());
        $this->partenaire->setActive(false);
        self::assertFalse($this->partenaire->isActive());
    }

    public function testLogoNullable(): void
    {
        $this->partenaire->setLogo('logo.png');
        self::assertSame('logo.png', $this->partenaire->getLogo());

        $this->partenaire->setLogo(null);
        self::assertNull($this->partenaire->getLogo());
    }

    public function testUrlNullable(): void
    {
        $this->partenaire->setUrl('https://forem.be');
        self::assertSame('https://forem.be', $this->partenaire->getUrl());

        $this->partenaire->setUrl(null);
        self::assertNull($this->partenaire->getUrl());
    }

    public function testSetLogoFileNullDoesNotSetUpdatedAt(): void
    {
        $this->partenaire->setLogoFile(null);
        self::assertNull($this->partenaire->getUpdatedAt());
    }

    public function testSetLogoFileSetsUpdatedAt(): void
    {
        $before = new \DateTimeImmutable();
        $file = $this->createStub(File::class);
        $this->partenaire->setLogoFile($file);

        self::assertSame($file, $this->partenaire->getLogoFile());
        self::assertInstanceOf(\DateTimeImmutable::class, $this->partenaire->getUpdatedAt());
        self::assertGreaterThanOrEqual($before, $this->partenaire->getUpdatedAt());
    }

    public function testUpdatedAtSetterAndGetter(): void
    {
        $date = new \DateTimeImmutable('2026-01-15 10:00:00');
        $this->partenaire->setUpdatedAt($date);
        self::assertSame($date, $this->partenaire->getUpdatedAt());

        $this->partenaire->setUpdatedAt(null);
        self::assertNull($this->partenaire->getUpdatedAt());
    }
}
