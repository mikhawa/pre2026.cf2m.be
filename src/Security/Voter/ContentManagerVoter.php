<?php

declare(strict_types=1);

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter sans sujet pour les ressources gérées à la fois par ROLE_ADMIN et ROLE_PEDAGO.
 *
 * Utiliser l'attribut CONTENT_MANAGER dans setPermission(), isGranted() et denyAccessUnlessGranted()
 * partout où ROLE_ADMIN et ROLE_PEDAGO doivent avoir les mêmes droits
 * (Pages, Inscriptions, Utilisateurs, Messages de contact).
 */
class ContentManagerVoter extends Voter
{
    /** Accordé à ROLE_ADMIN et ROLE_PEDAGO */
    public const CONTENT_MANAGER = 'CONTENT_MANAGER';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::CONTENT_MANAGER === $attribute;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        return $this->security->isGranted('ROLE_ADMIN')
            || $this->security->isGranted('ROLE_PEDAGO');
    }
}
