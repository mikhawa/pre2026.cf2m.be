<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Formation;
use App\Entity\Page;
use App\Entity\Rating;
use App\Entity\User;
use App\Entity\Works;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testDefaultValues(): void
    {
        self::assertNull($this->user->getId());
        self::assertNull($this->user->getEmail());
        self::assertNull($this->user->getUserName());
        self::assertNull($this->user->getBiography());
        self::assertNull($this->user->getCreatedAt());
        self::assertNull($this->user->getUpdatedAt());
        self::assertNull($this->user->getPlainPassword());
        self::assertNull($this->user->getActivationToken());
        self::assertSame(0, $this->user->getStatus());
        self::assertSame(['ROLE_USER'], $this->user->getRoles());
    }

    public function testCollectionsInitialized(): void
    {
        self::assertCount(0, $this->user->getComments());
        self::assertCount(0, $this->user->getRatings());
        self::assertCount(0, $this->user->getFormations());
        self::assertCount(0, $this->user->getWorks());
        self::assertCount(0, $this->user->getPages());
    }

    public function testSettersReturnStatic(): void
    {
        self::assertSame($this->user, $this->user->setEmail('test@cf2m.be'));
        self::assertSame($this->user, $this->user->setUserName('testuser'));
        self::assertSame($this->user, $this->user->setPassword('hashed'));
        self::assertSame($this->user, $this->user->setStatus(1));
        self::assertSame($this->user, $this->user->setBiography('bio'));
        self::assertSame($this->user, $this->user->setRoles(['ROLE_ADMIN']));
        self::assertSame($this->user, $this->user->setActivationToken('token'));
        self::assertSame($this->user, $this->user->setPlainPassword('secret'));
        self::assertSame($this->user, $this->user->setExternalLink1('https://cf2m.be'));
        self::assertSame($this->user, $this->user->setExternalLink2('https://cf2m.be'));
        self::assertSame($this->user, $this->user->setExternalLink3('https://cf2m.be'));
        self::assertSame($this->user, $this->user->setUpdatedAt(new \DateTimeImmutable()));
        self::assertSame($this->user, $this->user->setResetPasswordToken('reset'));
    }

    public function testToStringEmpty(): void
    {
        self::assertSame('', (string) $this->user);
    }

    public function testToString(): void
    {
        $this->user->setUserName('mikhawa');
        self::assertSame('mikhawa', (string) $this->user);
    }

    public function testSetCreatedAtValue(): void
    {
        self::assertNull($this->user->getCreatedAt());
        $this->user->setCreatedAtValue();
        self::assertInstanceOf(\DateTimeImmutable::class, $this->user->getCreatedAt());
    }

    public function testGetRolesAlwaysContainsRoleUser(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        self::assertContains('ROLE_USER', $this->user->getRoles());
        self::assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testGetRolesDeduplication(): void
    {
        $this->user->setRoles(['ROLE_USER', 'ROLE_USER']);
        self::assertCount(1, $this->user->getRoles());
    }

    public function testEraseCredentials(): void
    {
        $this->user->setPlainPassword('secret');
        self::assertSame('secret', $this->user->getPlainPassword());
        $this->user->eraseCredentials();
        self::assertNull($this->user->getPlainPassword());
    }

    public function testGetUserIdentifier(): void
    {
        $this->user->setEmail('test@cf2m.be');
        self::assertSame('test@cf2m.be', $this->user->getUserIdentifier());
    }

    public function testGetUserIdentifierThrowsWhenEmailNull(): void
    {
        $this->expectException(\LogicException::class);
        $this->user->getUserIdentifier();
    }

    public function testAddRemoveComment(): void
    {
        $comment = new Comment();
        $this->user->addComment($comment);
        self::assertCount(1, $this->user->getComments());
        self::assertSame($this->user, $comment->getUser());

        $this->user->addComment($comment);
        self::assertCount(1, $this->user->getComments());

        $this->user->removeComment($comment);
        self::assertCount(0, $this->user->getComments());
        self::assertNull($comment->getUser());
    }

    public function testAddRemoveRating(): void
    {
        $rating = new Rating();
        $this->user->addRating($rating);
        self::assertCount(1, $this->user->getRatings());
        self::assertSame($this->user, $rating->getUser());

        $this->user->addRating($rating);
        self::assertCount(1, $this->user->getRatings());

        $this->user->removeRating($rating);
        self::assertCount(0, $this->user->getRatings());
    }

    public function testAddRemoveFormation(): void
    {
        $formation = new Formation();
        $this->user->addFormation($formation);
        self::assertCount(1, $this->user->getFormations());
        self::assertCount(1, $formation->getResponsables());

        $this->user->addFormation($formation);
        self::assertCount(1, $this->user->getFormations());

        $this->user->removeFormation($formation);
        self::assertCount(0, $this->user->getFormations());
        self::assertCount(0, $formation->getResponsables());
    }

    public function testAddRemoveWork(): void
    {
        $works = new Works();
        $this->user->addWork($works);
        self::assertCount(1, $this->user->getWorks());
        self::assertCount(1, $works->getUsers());

        $this->user->addWork($works);
        self::assertCount(1, $this->user->getWorks());

        $this->user->removeWork($works);
        self::assertCount(0, $this->user->getWorks());
    }

    public function testAddRemovePage(): void
    {
        $page = new Page();
        $this->user->addPage($page);
        self::assertCount(1, $this->user->getPages());
        self::assertCount(1, $page->getUsers());

        $this->user->addPage($page);
        self::assertCount(1, $this->user->getPages());

        $this->user->removePage($page);
        self::assertCount(0, $this->user->getPages());
    }
}
