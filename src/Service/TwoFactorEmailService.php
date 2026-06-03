<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Gestion de la double authentification par email.
 * Génère, envoie et valide le code à 6 chiffres.
 */
class TwoFactorEmailService
{
    /** Durée de validité du code en minutes */
    private const CODE_TTL_MINUTES = 15;

    /** Rôles nécessitant une double authentification */
    public const ROLES_REQUIRING_2FA = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_PEDAGO'];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailerInterface $mailer,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
    ) {
    }

    /**
     * Détermine si un utilisateur doit passer la double authentification.
     */
    public function requiresTwoFactor(User $user): bool
    {
        foreach (self::ROLES_REQUIRING_2FA as $role) {
            if (in_array($role, $user->getRoles(), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Génère un code à 6 chiffres, le persiste et envoie l'email.
     */
    public function generateAndSendCode(User $user): void
    {
        $code = sprintf('%06d', random_int(0, 999999));

        $user->setTwoFactorCode($code);
        $user->setTwoFactorCodeExpiresAt(
            new \DateTimeImmutable('+'.self::CODE_TTL_MINUTES.' minutes')
        );
        $this->em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailFrom, 'CF2m — Sécurité'))
            ->to((string) $user->getEmail())
            ->subject('[CF2m] Votre code de connexion : '.$code)
            ->htmlTemplate('emails/two_factor_code.html.twig')
            ->context([
                'user' => $user,
                'code' => $code,
                'ttl' => self::CODE_TTL_MINUTES,
            ]);

        $this->mailer->send($email);
    }

    /**
     * Valide le code saisi.
     * Efface le code en base après succès ou expiration.
     * Retourne true si le code est correct et non expiré.
     */
    public function validateCode(User $user, string $code): bool
    {
        $storedCode = $user->getTwoFactorCode();
        $expiresAt = $user->getTwoFactorCodeExpiresAt();

        // Pas de code en attente
        if (null === $storedCode || null === $expiresAt) {
            return false;
        }

        // Code expiré — on nettoie
        if ($expiresAt < new \DateTimeImmutable()) {
            $this->clearCode($user);

            return false;
        }

        // Comparaison à temps constant pour éviter les timing attacks
        if (!hash_equals($storedCode, $code)) {
            return false;
        }

        // Succès — on nettoie le code pour usage unique
        $this->clearCode($user);

        return true;
    }

    /**
     * Efface le code 2FA de l'utilisateur.
     */
    public function clearCode(User $user): void
    {
        $user->setTwoFactorCode(null);
        $user->setTwoFactorCodeExpiresAt(null);
        $this->em->flush();
    }
}
