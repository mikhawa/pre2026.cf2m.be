<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Formation;
use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Works;
use PHPUnit\Framework\TestCase;

class WorksTest extends TestCase
{
    private Works $works;

    protected function setUp(): void
    {
        $this->works = new Works();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->works->getId());
        self::assertNull($this->works->getTitle());
        self::assertNull($this->works->getSlug());
        self::assertNull($this->works->getDescription());
        self::assertNull($this->works->getCreatedAt());
        self::assertNull($this->works->getPublishedAt());
        self::assertNull($this->works->getFormation());
        self::assertSame('draft', $this->works->getStatus());
    }

    public function testCollectionsInitialized(): void
    {
        self::assertCount(0, $this->works->getComments());
        self::assertCount(0, $this->works->getUsers());
        self::assertCount(0, $this->works->getRatings());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->works, $this->works->setTitle('Mon travail'));
        self::assertSame($this->works, $this->works->setSlug('mon-travail'));
        self::assertSame($this->works, $this->works->setDescription('Description'));
        self::assertSame($this->works, $this->works->setStatus('published'));
        self::assertSame($this->works, $this->works->setPublishedAt(new \DateTimeImmutable()));
        self::assertSame($this->works, $this->works->setFormation(new Formation()));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->works);
    }

    public function testToString(): void
    {
        $this->works->setTitle('Projet final');
        self::assertSame('Projet final', (string) $this->works);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->works->getCreatedAt());
        $this->works->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->works->getCreatedAt());
    }

    public function testAddRemoveComment(): void
    {
        $comment = new Comment();
        $this->works->addComment($comment);
        self::assertCount(1, $this->works->getComments());
        self::assertSame($this->works, $comment->getWorks());

        $this->works->addComment($comment);
        self::assertCount(1, $this->works->getComments());

        $this->works->removeComment($comment);
        self::assertCount(0, $this->works->getComments());
        self::assertNull($comment->getWorks());
    }

    public function testAddRemoveUser(): void
    {
        $user = new User();
        $this->works->addUser($user);
        self::assertCount(1, $this->works->getUsers());
        self::assertCount(1, $user->getWorks());

        $this->works->addUser($user);
        self::assertCount(1, $this->works->getUsers());

        $this->works->removeUser($user);
        self::assertCount(0, $this->works->getUsers());
    }

    public function testAddRemoveRating(): void
    {
        $rating = new Rating();
        $this->works->addRating($rating);
        self::assertCount(1, $this->works->getRatings());
        self::assertCount(1, $rating->getWorks());

        $this->works->addRating($rating);
        self::assertCount(1, $this->works->getRatings());

        $this->works->removeRating($rating);
        self::assertCount(0, $this->works->getRatings());
    }

    public function testFormationRelation(): void
    {
        $formation = new Formation();
        $this->works->setFormation($formation);
        self::assertSame($formation, $this->works->getFormation());
    }
}
