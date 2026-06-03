<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Works;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter pour les permissions contextuelles sur les Works.
 *
 * Un formateur responsable de la Formation parente d'un Works obtient les mêmes
 * droits qu'un ROLE_FORMATEUR (approuver/rejeter/restaurer) sur CE works uniquement.
 */
class WorksVoter extends Voter
{
    /** Modification auto-approuvée (pas de révision PENDING) */
    public const EDIT_AUTOAPPROVE = 'WORKS_EDIT_AUTOAPPROVE';

    /** Approbation d'une révision en attente */
    public const APPROVE = 'WORKS_APPROVE';

    /** Rejet d'une révision en attente */
    public const REJECT = 'WORKS_REJECT';

    /** Restauration d'une version antérieure */
    public const RESTORE = 'WORKS_RESTORE';

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
            && $subject instanceof Works;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Works $works */
        $works = $subject;
        $formation = $works->getFormation();

        // Les admins et super-admins ont toujours accès
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Les non-formateurs n'ont jamais accès
        if (!$this->security->isGranted('ROLE_FORMATEUR')) {
            return false;
        }

        // Un works sans formation parente ne peut pas être approuvé contextuellement
        if (null === $formation) {
            return false;
        }

        // Un formateur a accès uniquement s'il est responsable de la formation parente
        return $formation->getResponsables()->contains($user);
    }
}
