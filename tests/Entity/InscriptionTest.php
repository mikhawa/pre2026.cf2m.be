<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class InscriptionTest extends TestCase
{
    private Inscription $inscription;

    protected function setUp(): void
    {
        $this->inscription = new Inscription();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->inscription->getId());
        self::assertNull($this->inscription->getNom());
        self::assertNull($this->inscription->getPrenom());
        self::assertNull($this->inscription->getEmail());
        self::assertNull($this->inscription->getMessage());
        self::assertNull($this->inscription->getCreatedAt());
        self::assertNull($this->inscription->getTreatAt());
        self::assertNull($this->inscription->getFormation());
        self::assertNull($this->inscription->getTreatBy());
        self::assertFalse($this->inscription->isTreat());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->inscription, $this->inscription->setNom('Dupont'));
        self::assertSame($this->inscription, $this->inscription->setPrenom('Jean'));
        self::assertSame($this->inscription, $this->inscription->setEmail('jean@cf2m.be'));
        self::assertSame($this->inscription, $this->inscription->setMessage('Je suis motivé'));
        self::assertSame($this->inscription, $this->inscription->setTreat(true));
        self::assertSame($this->inscription, $this->inscription->setTreatAt(new \DateTimeImmutable()));
        self::assertSame($this->inscription, $this->inscription->setFormation(new Formation()));
        self::assertSame($this->inscription, $this->inscription->setTreatBy(new User()));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame(' ', (string) $this->inscription);
    }

    public function testToString(): void
    {
        $this->inscription->setPrenom('Jean');
        $this->inscription->setNom('Dupont');
        self::assertSame('Jean Dupont', (string) $this->inscription);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->inscription->getCreatedAt());
        $this->inscription->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->inscription->getCreatedAt());
    }

    public function testTreatToggle(): void
    {
        self::assertFalse($this->inscription->isTreat());
        $this->inscription->setTreat(true);
        self::assertTrue($this->inscription->isTreat());
    }

    public function testTreatByNullable(): void
    {
        $user = new User();
        $this->inscription->setTreatBy($user);
        self::assertSame($user, $this->inscription->getTreatBy());

        $this->inscription->setTreatBy(null);
        self::assertNull($this->inscription->getTreatBy());
    }

    public function testFormationRelation(): void
    {
        $formation = new Formation();
        $this->inscription->setFormation($formation);
        self::assertSame($formation, $this->inscription->getFormation());
    }
}
