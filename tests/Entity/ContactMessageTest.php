<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ContactMessage;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ContactMessageTest extends TestCase
{
    private ContactMessage $contactMessage;

    protected function setUp(): void
    {
        $this->contactMessage = new ContactMessage();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->contactMessage->getId());
        self::assertNull($this->contactMessage->getNom());
        self::assertNull($this->contactMessage->getEmail());
        self::assertNull($this->contactMessage->getSujet());
        self::assertNull($this->contactMessage->getMessage());
        self::assertNull($this->contactMessage->getCreatedAt());
        self::assertNull($this->contactMessage->getReadBy());
        self::assertFalse($this->contactMessage->isRead());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->contactMessage, $this->contactMessage->setNom('Martin'));
        self::assertSame($this->contactMessage, $this->contactMessage->setEmail('martin@test.be'));
        self::assertSame($this->contactMessage, $this->contactMessage->setSujet('Demande info'));
        self::assertSame($this->contactMessage, $this->contactMessage->setMessage('Bonjour'));
        self::assertSame($this->contactMessage, $this->contactMessage->setRead(true));
        self::assertSame($this->contactMessage, $this->contactMessage->setReadBy(new User()));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->contactMessage);
    }

    public function testToString(): void
    {
        $this->contactMessage->setSujet('Demande de partenariat');
        self::assertSame('Demande de partenariat', (string) $this->contactMessage);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->contactMessage->getCreatedAt());
        $this->contactMessage->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->contactMessage->getCreatedAt());
    }

    public function testIsReadToggle(): void
    {
        self::assertFalse($this->contactMessage->isRead());
        $this->contactMessage->setRead(true);
        self::assertTrue($this->contactMessage->isRead());
        $this->contactMessage->setRead(false);
        self::assertFalse($this->contactMessage->isRead());
    }

    public function testReadByNullable(): void
    {
        $user = new User();
        $this->contactMessage->setReadBy($user);
        self::assertSame($user, $this->contactMessage->getReadBy());

        $this->contactMessage->setReadBy(null);
        self::assertNull($this->contactMessage->getReadBy());
    }
}
