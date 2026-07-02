<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Formation;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter régissant QUI peut gérer les stagiaires d'une Formation (ajout/retrait).
 *
 * Attention : ce voter ne concerne pas les droits du stagiaire lui-même
 * (ROLE_STAGIAIRE, obtenu par synchronisation via StagiaireService), mais uniquement
 * les gestionnaires : admin/pédago partout, formateur seulement sur ses formations.
 */
class FormationStagiaireVoter extends Voter
{
    /** Gestion (ajout/retrait) des stagiaires d'une Formation donnée */
    public const MANAGE_STAGIAIRES = 'FORMATION_MANAGE_STAGIAIRES';

    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (self::MANAGE_STAGIAIRES !== $attribute) {
            return false;
        }

        return $subject instanceof Formation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Formation $formation */
        $formation = $subject;

        // Les admins, super-admins et pédagos peuvent gérer toutes les formations
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_PEDAGO')) {
            return true;
        }

        // Les non-formateurs n'ont jamais accès
        if (!$this->security->isGranted('ROLE_FORMATEUR')) {
            return false;
        }

        // Un formateur peut gérer les stagiaires uniquement s'il est responsable de cette formation
        return $formation->getResponsables()->contains($user);
    }
}
