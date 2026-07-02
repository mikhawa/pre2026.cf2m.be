<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Formation;
use App\Entity\User;
use App\Security\Voter\FormationStagiaireVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class FormationStagiaireVoterTest extends TestCase
{
    private function buildVoter(bool $isAdmin, bool $isPedago, bool $isFormateur): FormationStagiaireVoter
    {
        $security = $this->createStub(Security::class);
        $security->method('isGranted')->willReturnCallback(
            fn (string $role) => match ($role) {
                'ROLE_ADMIN' => $isAdmin,
                'ROLE_PEDAGO' => $isPedago,
                'ROLE_FORMATEUR' => $isFormateur,
                default => false,
            }
        );

        return new FormationStagiaireVoter($security);
    }

    private function token(?User $user = null): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user ?? new User());

        return $token;
    }

    // ── Supports ────────────────────────────────────────────────────────────

    public function testSupportsManageAttributeWithFormation(): void
    {
        $voter = $this->buildVoter(false, false, false);

        self::assertNotSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), new Formation(), [FormationStagiaireVoter::MANAGE_STAGIAIRES]),
            'Le voter doit supporter FORMATION_MANAGE_STAGIAIRES avec une Formation'
        );
    }

    public function testAbstainsOnUnknownAttribute(): void
    {
        $voter = $this->buildVoter(true, false, false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), new Formation(), ['UNKNOWN_ATTR'])
        );
    }

    public function testAbstainsWhenSubjectIsNotFormation(): void
    {
        $voter = $this->buildVoter(true, false, false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), new \stdClass(), [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }

    // ── FORMATION_MANAGE_STAGIAIRES ─────────────────────────────────────────

    public function testGrantedForAdmin(): void
    {
        $voter = $this->buildVoter(isAdmin: true, isPedago: false, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), new Formation(), [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }

    public function testGrantedForPedago(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: true, isFormateur: false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), new Formation(), [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }

    public function testGrantedForFormateurResponsable(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);
        $user = new User();
        $formation = new Formation();
        $formation->addResponsable($user);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($user), $formation, [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }

    public function testDeniedForFormateurNonResponsable(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);
        $formation = new Formation();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $formation, [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }

    public function testDeniedForStagiaire(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);
        $formation = new Formation();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $formation, [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }

    public function testDeniedWhenTokenUserIsNotAppUser(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, new Formation(), [FormationStagiaireVoter::MANAGE_STAGIAIRES])
        );
    }
}
