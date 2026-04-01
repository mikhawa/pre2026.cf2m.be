<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Formation;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter pour les permissions contextuelles sur les Formations.
 *
 * Un formateur désigné comme responsable d'une Formation obtient les mêmes
 * droits qu'un ROLE_ADMIN sur CETTE formation uniquement.
 */
class FormationVoter extends Voter
{
    /** Modification auto-approuvée (pas de révision PENDING) */
    public const EDIT_AUTOAPPROVE = 'FORMATION_EDIT_AUTOAPPROVE';

    /** Approbation d'une révision en attente */
    public const APPROVE = 'FORMATION_APPROVE';

    /** Rejet d'une révision en attente */
    public const REJECT = 'FORMATION_REJECT';

    /** Restauration d'une version antérieure */
    public const RESTORE = 'FORMATION_RESTORE';

    private const ATTRIBUTES = [
        self::EDIT_AUTOAPPROVE,
        self::APPROVE,
        self::REJECT,
        self::RESTORE,
    ];

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, self::ATTRIBUTES, true)
            && $subject instanceof Formation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Formation $formation */
        $formation = $subject;

        // Les admins et super-admins ont toujours accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Les non-formateurs n'ont jamais accès
        if (!$this->security->isGranted('ROLE_FORMATEUR')) {
            return false;
        }

        // Un formateur a accès uniquement s'il est responsable de cette formation
        return $formation->getResponsables()->contains($user);
    }
}
