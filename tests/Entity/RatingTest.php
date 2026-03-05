<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Works;
use PHPUnit\Framework\TestCase;

class RatingTest extends TestCase
{
    private Rating $rating;

    protected function setUp(): void
    {
        $this->rating = new Rating();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->rating->getId());
        self::assertNull($this->rating->getValue());
        self::assertNull($this->rating->getCreatedAt());
        self::assertNull($this->rating->getUser());
    }

    public function testCollectionsInitialized(): void
    {
        self::assertCount(0, $this->rating->getComments());
        self::assertCount(0, $this->rating->getWorks());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->rating, $this->rating->setValue(4));
        self::assertSame($this->rating, $this->rating->setUser(new User()));
    }

    public function testToStringNull(): void
    {
        self::assertSame('', (string) $this->rating);
    }

    public function testToString(): void
    {
        $this->rating->setValue(5);
        self::assertSame('5', (string) $this->rating);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->rating->getCreatedAt());
        $this->rating->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->rating->getCreatedAt());
    }

    public function testSetValue(): void
    {
        $this->rating->setValue(3);
        self::assertSame(3, $this->rating->getValue());
    }

    public function testAddRemoveComment(): void
    {
        $comment = new Comment();
        $this->rating->addComment($comment);
        self::assertCount(1, $this->rating->getComments());
        self::assertCount(1, $comment->getRatings());

        $this->rating->addComment($comment);
        self::assertCount(1, $this->rating->getComments());

        $this->rating->removeComment($comment);
        self::assertCount(0, $this->rating->getComments());
    }

    public function testAddRemoveWork(): void
    {
        $works = new Works();
        $this->rating->addWork($works);
        self::assertCount(1, $this->rating->getWorks());
        self::assertCount(1, $works->getRatings());

        $this->rating->addWork($works);
        self::assertCount(1, $this->rating->getWorks());

        $this->rating->removeWork($works);
        self::assertCount(0, $this->rating->getWorks());
    }

    public function testUserRelation(): void
    {
        $user = new User();
        $this->rating->setUser($user);
        self::assertSame($user, $this->rating->getUser());
    }
}
