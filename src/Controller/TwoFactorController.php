<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\TwoFactorEmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Gestion de la double authentification par email.
 */
class TwoFactorController extends AbstractController
{
    public function __construct(
        private readonly TwoFactorEmailService $twoFactorEmailService,
    ) {
    }

    /**
     * Affiche et traite le formulaire de saisie du code 2FA.
     */
    #[Route('/double-authentification', name: 'app_two_factor')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        // Non connecté
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Ne nécessite pas de 2FA — rediriger vers le profil
        if (!$this->twoFactorEmailService->requiresTwoFactor($user)) {
            return $this->redirectToRoute('app_profile');
        }

        // 2FA déjà validé pour cette session
        if ($request->getSession()->get('2fa_verified') === true) {
            return $this->redirectToRoute('app_profile');
        }

        $error   = null;
        $expired = false;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('two_factor', (string) $request->request->get('_csrf_token'))) {
                $error = 'Jeton CSRF invalide. Veuillez réessayer.';
            } else {
                // Code expiré
                $expiresAt = $user->getTwoFactorCodeExpiresAt();
                if ($expiresAt !== null && $expiresAt < new \DateTimeImmutable()) {
                    $expired = true;
                    $this->twoFactorEmailService->clearCode($user);
                } else {
                    $code = trim((string) $request->request->get('code', ''));

                    if ($this->twoFactorEmailService->validateCode($user, $code)) {
                        $request->getSession()->set('2fa_verified', true);

                        return $this->redirectToRoute('app_profile');
                    }

                    $error = 'Code incorrect. Veuillez vérifier votre email et réessayer.';
                }
            }
        }

        // Obfusquer l'adresse email affichée (ex: mi***@cf2m.be)
        $rawEmail    = (string) $user->getEmail();
        $parts       = explode('@', $rawEmail, 2);
        $local       = $parts[0];
        $domain      = $parts[1] ?? '';
        $obfuscated  = substr($local, 0, min(3, strlen($local))) . '***@' . $domain;

        return $this->render('security/two_factor.html.twig', [
            'error'      => $error,
            'expired'    => $expired,
            'email'      => $obfuscated,
        ]);
    }

    /**
     * Renvoie un nouveau code par email.
     */
    #[Route('/double-authentification/renvoyer', name: 'app_two_factor_resend', methods: ['POST'])]
    public function resend(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('two_factor_resend', (string) $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide. Veuillez réessayer.');

            return $this->redirectToRoute('app_two_factor');
        }

        $this->twoFactorEmailService->generateAndSendCode($user);

        $this->addFlash('success', 'Un nouveau code vous a été envoyé par email.');

        return $this->redirectToRoute('app_two_factor');
    }
}
