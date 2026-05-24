<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Security\Voter\ContentManagerVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ContentManagerVoterTest extends TestCase
{
    private function buildVoter(bool $isAdmin, bool $isPedago): ContentManagerVoter
    {
        $security = $this->createStub(Security::class);
        $security->method('isGranted')->willReturnCallback(
            fn (string $role) => match ($role) {
                'ROLE_ADMIN' => $isAdmin,
                'ROLE_PEDAGO' => $isPedago,
                default => false,
            }
        );

        return new ContentManagerVoter($security);
    }

    private function token(): TokenInterface
    {
        return $this->createStub(TokenInterface::class);
    }

    // ── Supports ────────────────────────────────────────────────────────────

    public function testSupportsContentManagerAttribute(): void
    {
        $voter = $this->buildVoter(false, false);

        // Doit voter (pas ABSTAIN) sur CONTENT_MANAGER, quel que soit le sujet
        self::assertNotSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    public function testSupportsContentManagerWithAnySubject(): void
    {
        $voter = $this->buildVoter(true, false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), new \stdClass(), [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    public function testAbstainsOnUnknownAttribute(): void
    {
        $voter = $this->buildVoter(true, false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), null, ['UNKNOWN_ATTR'])
        );
    }

    // ── ROLE_ADMIN ───────────────────────────────────────────────────────────

    public function testGrantedForAdmin(): void
    {
        $voter = $this->buildVoter(isAdmin: true, isPedago: false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    // ── ROLE_PEDAGO ──────────────────────────────────────────────────────────

    public function testGrantedForPedago(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    public function testGrantedWhenBothAdminAndPedago(): void
    {
        $voter = $this->buildVoter(isAdmin: true, isPedago: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    // ── Rôles inférieurs ─────────────────────────────────────────────────────

    public function testDeniedForFormateur(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    public function testDeniedForStagiaire(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }

    public function testDeniedForAnonymous(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), null, [ContentManagerVoter::CONTENT_MANAGER])
        );
    }
}
