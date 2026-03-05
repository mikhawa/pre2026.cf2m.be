<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Partenaire;
use PHPUnit\Framework\TestCase;

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
        self::assertNull($this->partenaire->getUrl());
        self::assertFalse($this->partenaire->isActive());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->partenaire, $this->partenaire->setNom('FOREM'));
        self::assertSame($this->partenaire, $this->partenaire->setDescription('Organisme public'));
        self::assertSame($this->partenaire, $this->partenaire->setLogo('forem.png'));
        self::assertSame($this->partenaire, $this->partenaire->setUrl('https://forem.be'));
        self::assertSame($this->partenaire, $this->partenaire->setActive(true));
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
}
