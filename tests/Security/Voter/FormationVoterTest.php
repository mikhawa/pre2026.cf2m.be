<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Formation;
use App\Entity\User;
use App\Security\Voter\FormationVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class FormationVoterTest extends TestCase
{
    private function buildVoter(bool $isAdmin, bool $isPedago, bool $isFormateur): FormationVoter
    {
        $security = $this->createStub(Security::class);
        $security->method('isGranted')->willReturnCallback(
            fn (string $role) => match ($role) {
                'ROLE_ADMIN'     => $isAdmin,
                'ROLE_PEDAGO'    => $isPedago,
                'ROLE_FORMATEUR' => $isFormateur,
                default          => false,
            }
        );

        return new FormationVoter($security);
    }

    private function token(?User $user = null): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user ?? new User());

        return $token;
    }

    // ── Supports ────────────────────────────────────────────────────────────

    public function testSupportsFormationAttributes(): void
    {
        $voter     = $this->buildVoter(false, false, false);
        $formation = new Formation();

        foreach ([
            FormationVoter::EDIT_AUTOAPPROVE,
            FormationVoter::APPROVE,
            FormationVoter::REJECT,
            FormationVoter::RESTORE,
        ] as $attribute) {
            self::assertNotSame(
                VoterInterface::ACCESS_ABSTAIN,
                $voter->vote($this->token(), $formation, [$attribute]),
                "Le voter doit supporter l'attribut $attribute avec une Formation"
            );
        }
    }

    public function testSupportsCreateWithoutSubject(): void
    {
        $voter = $this->buildVoter(true, false, false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), null, [FormationVoter::CREATE])
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
            $voter->vote($this->token(), new \stdClass(), [FormationVoter::APPROVE])
        );
    }

    // ── FORMATION_CREATE ────────────────────────────────────────────────────

    public function testCreateGrantedForAdmin(): void
    {
        $voter = $this->buildVoter(isAdmin: true, isPedago: false, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), null, [FormationVoter::CREATE])
        );
    }

    public function testCreateGrantedForPedago(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: true, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), null, [FormationVoter::CREATE])
        );
    }

    public function testCreateDeniedForFormateurNonResponsable(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), null, [FormationVoter::CREATE])
        );
    }

    public function testCreateDeniedForStagiaire(): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), null, [FormationVoter::CREATE])
        );
    }

    // ── FORMATION_APPROVE / EDIT_AUTOAPPROVE (logique identique) ────────────

    #[DataProvider('contextualAttributes')]
    public function testGrantedForAdmin(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: true, isPedago: false, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), new Formation(), [$attribute])
        );
    }

    #[DataProvider('contextualAttributes')]
    public function testGrantedForPedago(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: true, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), new Formation(), [$attribute])
        );
    }

    #[DataProvider('contextualAttributes')]
    public function testGrantedForFormateurResponsable(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);
        $user  = new User();
        $formation = new Formation();
        $formation->addResponsable($user);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($user), $formation, [$attribute])
        );
    }

    #[DataProvider('contextualAttributes')]
    public function testDeniedForFormateurNonResponsable(string $attribute): void
    {
        $voter     = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);
        $formation = new Formation();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $formation, [$attribute])
        );
    }

    #[DataProvider('contextualAttributes')]
    public function testDeniedForStagiaire(string $attribute): void
    {
        $voter     = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);
        $formation = new Formation();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $formation, [$attribute])
        );
    }

    #[DataProvider('contextualAttributes')]
    public function testDeniedWhenTokenUserIsNotAppUser(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, new Formation(), [$attribute])
        );
    }

    /** @return array<string, array{string}> */
    public static function contextualAttributes(): array
    {
        return [
            'EDIT_AUTOAPPROVE' => [FormationVoter::EDIT_AUTOAPPROVE],
            'APPROVE'          => [FormationVoter::APPROVE],
            'REJECT'           => [FormationVoter::REJECT],
            'RESTORE'          => [FormationVoter::RESTORE],
        ];
    }
}
