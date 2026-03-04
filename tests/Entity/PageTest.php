<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Page;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    private Page $page;

    protected function setUp(): void
    {
        $this->page = new Page();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->page->getId());
        self::assertNull($this->page->getTitle());
        self::assertNull($this->page->getSlug());
        self::assertNull($this->page->getContent());
        self::assertNull($this->page->getCreatedAt());
        self::assertNull($this->page->getPublishedAt());
        self::assertSame('draft', $this->page->getStatus());
    }

    public function testCollectionInitialized(): void
    {
        self::assertCount(0, $this->page->getUsers());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->page, $this->page->setTitle('À propos'));
        self::assertSame($this->page, $this->page->setSlug('a-propos'));
        self::assertSame($this->page, $this->page->setContent('<p>Contenu</p>'));
        self::assertSame($this->page, $this->page->setStatus('published'));
        self::assertSame($this->page, $this->page->setPublishedAt(new \DateTimeImmutable()));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->page);
    }

    public function testToString(): void
    {
        $this->page->setTitle('Accueil');
        self::assertSame('Accueil', (string) $this->page);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->page->getCreatedAt());
        $this->page->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->page->getCreatedAt());
    }

    public function testAddRemoveUser(): void
    {
        $user = new User();
        $this->page->addUser($user);
        self::assertCount(1, $this->page->getUsers());
        self::assertCount(1, $user->getPages());

        $this->page->addUser($user);
        self::assertCount(1, $this->page->getUsers());

        $this->page->removeUser($user);
        self::assertCount(0, $this->page->getUsers());
        self::assertCount(0, $user->getPages());
    }

    public function testMultipleUsers(): void
    {
        $user1 = new User();
        $user2 = new User();
        $this->page->addUser($user1);
        $this->page->addUser($user2);
        self::assertCount(2, $this->page->getUsers());
    }
}
