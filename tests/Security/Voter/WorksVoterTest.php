<?php

declare(strict_types=1);

namespace App\Tests\Security\Voter;

use App\Entity\Formation;
use App\Entity\User;
use App\Entity\Works;
use App\Security\Voter\WorksVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class WorksVoterTest extends TestCase
{
    private function buildVoter(bool $isAdmin, bool $isPedago, bool $isFormateur): WorksVoter
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

        return new WorksVoter($security);
    }

    private function token(?User $user = null): TokenInterface
    {
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn($user ?? new User());

        return $token;
    }

    private function worksWithFormation(?User $responsable = null): Works
    {
        $formation = new Formation();
        if (null !== $responsable) {
            $formation->addResponsable($responsable);
        }

        $works = new Works();
        $works->setFormation($formation);

        return $works;
    }

    // ── Supports ────────────────────────────────────────────────────────────

    public function testSupportsWorksAttributes(): void
    {
        $voter = $this->buildVoter(false, false, false);
        $works = $this->worksWithFormation();

        foreach ([
            WorksVoter::EDIT_AUTOAPPROVE,
            WorksVoter::APPROVE,
            WorksVoter::REJECT,
            WorksVoter::RESTORE,
        ] as $attribute) {
            self::assertNotSame(
                VoterInterface::ACCESS_ABSTAIN,
                $voter->vote($this->token(), $works, [$attribute]),
                "Le voter doit supporter l'attribut $attribute avec un Works"
            );
        }
    }

    public function testAbstainsOnUnknownAttribute(): void
    {
        $voter = $this->buildVoter(true, false, false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), $this->worksWithFormation(), ['UNKNOWN_ATTR'])
        );
    }

    public function testAbstainsWhenSubjectIsNotWorks(): void
    {
        $voter = $this->buildVoter(true, false, false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->token(), new \stdClass(), [WorksVoter::APPROVE])
        );
    }

    // ── Accès admin ─────────────────────────────────────────────────────────

    #[DataProvider('allAttributes')]
    public function testGrantedForAdmin(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: true, isPedago: false, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token(), $this->worksWithFormation(), [$attribute])
        );
    }

    // ── ROLE_PEDAGO n'a pas accès aux actions Works (lecture seule) ──────────

    #[DataProvider('allAttributes')]
    public function testDeniedForPedagoNonResponsable(string $attribute): void
    {
        // PEDAGO hérite de ROLE_FORMATEUR mais n'est pas responsable de cette formation
        $voter = $this->buildVoter(isAdmin: false, isPedago: true, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $this->worksWithFormation(), [$attribute])
        );
    }

    // ── Formateur responsable ────────────────────────────────────────────────

    #[DataProvider('allAttributes')]
    public function testGrantedForFormateurResponsable(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);
        $user = new User();
        $works = $this->worksWithFormation($user);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($user), $works, [$attribute])
        );
    }

    #[DataProvider('allAttributes')]
    public function testDeniedForFormateurNonResponsable(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $this->worksWithFormation(), [$attribute])
        );
    }

    // ── Formation parente absente ────────────────────────────────────────────

    #[DataProvider('allAttributes')]
    public function testDeniedWhenFormationIsNull(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: true);
        $works = new Works(); // pas de formation parente

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $works, [$attribute])
        );
    }

    // ── Stagiaire ────────────────────────────────────────────────────────────

    #[DataProvider('allAttributes')]
    public function testDeniedForStagiaire(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token(), $this->worksWithFormation(), [$attribute])
        );
    }

    // ── Token sans user App\Entity\User ─────────────────────────────────────

    #[DataProvider('allAttributes')]
    public function testDeniedWhenTokenUserIsNotAppUser(string $attribute): void
    {
        $voter = $this->buildVoter(isAdmin: false, isPedago: false, isFormateur: false);
        $token = $this->createStub(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, $this->worksWithFormation(), [$attribute])
        );
    }

    /** @return array<string, array{string}> */
    public static function allAttributes(): array
    {
        return [
            'EDIT_AUTOAPPROVE' => [WorksVoter::EDIT_AUTOAPPROVE],
            'APPROVE' => [WorksVoter::APPROVE],
            'REJECT' => [WorksVoter::REJECT],
            'RESTORE' => [WorksVoter::RESTORE],
        ];
    }
}
