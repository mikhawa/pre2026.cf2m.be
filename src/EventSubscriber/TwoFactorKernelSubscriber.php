<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\TwoFactorEmailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Vérifie sur chaque requête que les utilisateurs à rôle privilégié
 * ont bien complété la double authentification.
 * Redirige vers la page 2FA si ce n'est pas encore fait.
 */
class TwoFactorKernelSubscriber implements EventSubscriberInterface
{
    /** Routes accessibles sans avoir validé la double authentification */
    private const ROUTES_WHITELIST = [
        'app_two_factor',
        'app_two_factor_resend',
        'app_logout',
        'app_login',
    ];

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly TwoFactorEmailService $twoFactorEmailService,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // Priorité 0 — après le router listener (priorité 32), avant la sécurité (priorité 8)
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Route non encore résolue ou sous-requête interne
        if (!$request->attributes->has('_route')) {
            return;
        }

        // Pas de session active
        if (!$request->hasSession()) {
            return;
        }

        // 2FA déjà validé pour cette session
        if ($request->getSession()->get('2fa_verified') === true) {
            return;
        }

        // Vérifier si l'utilisateur est authentifié
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            return;
        }

        // L'utilisateur ne nécessite pas de 2FA
        if (!$this->twoFactorEmailService->requiresTwoFactor($user)) {
            return;
        }

        // Route dans la liste blanche (page 2FA, déconnexion, etc.)
        $routeName = (string) $request->attributes->get('_route', '');
        if (in_array($routeName, self::ROUTES_WHITELIST, true)) {
            return;
        }

        // Routes internes Symfony (profiler, wdt, etc.)
        if (str_starts_with($routeName, '_')) {
            return;
        }

        // Rediriger vers la page de double authentification
        $event->setResponse(
            new RedirectResponse($this->urlGenerator->generate('app_two_factor'))
        );
    }
}
