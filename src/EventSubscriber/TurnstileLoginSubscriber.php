<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Service\TurnstileVerifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

/**
 * Vérifie le token Cloudflare Turnstile lors de la tentative de connexion.
 */
class TurnstileLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TurnstileVerifier $turnstileVerifier,
        private readonly RequestStack $requestStack,
    ) {}

    public static function getSubscribedEvents(): array
    {
        // Priorité 0 — s'exécute avant la vérification des credentials (priorité -128)
        return [
            CheckPassportEvent::class => ['onCheckPassport', 0],
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null || !$request->isMethod('POST')) {
            return;
        }

        // Uniquement sur la route de connexion
        if ($request->attributes->get('_route') !== 'app_login') {
            return;
        }

        $token = (string) $request->request->get('cf-turnstile-response', '');

        if (!$this->turnstileVerifier->verify($token, $request->getClientIp())) {
            throw new CustomUserMessageAuthenticationException(
                'La vérification anti-robot a échoué. Veuillez réessayer.'
            );
        }
    }
}
