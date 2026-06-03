<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (0 === $user->getStatus()) {
            throw new CustomUserMessageAccountStatusException('Votre compte n\'est pas encore activé. Vérifiez votre boite mail.');
        }

        if (2 === $user->getStatus()) {
            throw new CustomUserMessageAccountStatusException('Votre compte a été suspendu. Contactez l\'administration pour plus d\'informations.');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
