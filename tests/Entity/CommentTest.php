<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Works;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    private Comment $comment;

    protected function setUp(): void
    {
        $this->comment = new Comment();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->comment->getId());
        self::assertNull($this->comment->getContent());
        self::assertNull($this->comment->getCreatedAt());
        self::assertNull($this->comment->getUser());
        self::assertNull($this->comment->getWorks());
        self::assertFalse($this->comment->isApproved());
    }

    public function testCollectionInitialized(): void
    {
        self::assertCount(0, $this->comment->getRatings());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->comment, $this->comment->setContent('Super travail !'));
        self::assertSame($this->comment, $this->comment->setApproved(true));
        self::assertSame($this->comment, $this->comment->setUser(new User()));
        self::assertSame($this->comment, $this->comment->setWorks(new Works()));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->comment);
    }

    public function testToStringShort(): void
    {
        $this->comment->setContent('Bon travail');
        self::assertSame('Bon travail', (string) $this->comment);
    }

    public function testToStringTruncatedAt50(): void
    {
        $longContent = str_repeat('a', 60);
        $this->comment->setContent($longContent);
        self::assertSame(str_repeat('a', 50), (string) $this->comment);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->comment->getCreatedAt());
        $this->comment->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->comment->getCreatedAt());
    }

    public function testIsApprovedToggle(): void
    {
        self::assertFalse($this->comment->isApproved());
        $this->comment->setApproved(true);
        self::assertTrue($this->comment->isApproved());
        $this->comment->setApproved(false);
        self::assertFalse($this->comment->isApproved());
    }

    public function testAddRemoveRating(): void
    {
        $rating = new Rating();
        $this->comment->addRating($rating);
        self::assertCount(1, $this->comment->getRatings());
        self::assertCount(1, $rating->getComments());

        $this->comment->addRating($rating);
        self::assertCount(1, $this->comment->getRatings());

        $this->comment->removeRating($rating);
        self::assertCount(0, $this->comment->getRatings());
    }

    public function testUserRelation(): void
    {
        $user = new User();
        $this->comment->setUser($user);
        self::assertSame($user, $this->comment->getUser());

        $this->comment->setUser(null);
        self::assertNull($this->comment->getUser());
    }

    public function testWorksRelation(): void
    {
        $works = new Works();
        $this->comment->setWorks($works);
        self::assertSame($works, $this->comment->getWorks());

        $this->comment->setWorks(null);
        self::assertNull($this->comment->getWorks());
    }
}
