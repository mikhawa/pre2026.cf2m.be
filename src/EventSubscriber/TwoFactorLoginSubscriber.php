<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\TwoFactorEmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Intercepte la connexion réussie et déclenche la double authentification
 * pour les utilisateurs ayant un rôle privilégié (ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_PEDAGO).
 */
class TwoFactorLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TwoFactorEmailService $twoFactorEmailService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$this->twoFactorEmailService->requiresTwoFactor($user)) {
            return;
        }

        $session = $event->getRequest()->getSession();

        // Marquer la session comme 2FA non encore validé
        $session->set('2fa_verified', false);

        // Symfony sauvegarde l'URL protégée tentée avant le login dans cette clé.
        // On la recopie pour notre propre redirection post-2FA.
        $targetPath = $session->get('_security.main.target_path');
        if ($targetPath !== null) {
            $session->set('2fa_target_path', $targetPath);
        }

        // Générer et envoyer le code par email
        $this->twoFactorEmailService->generateAndSendCode($user);

        // Rediriger vers la page de saisie du code (l'utilisateur est connecté mais 2FA non vérifié)
        $event->setResponse(
            new RedirectResponse($this->urlGenerator->generate('app_two_factor'))
        );
    }
}
