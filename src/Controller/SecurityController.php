<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirige vers le profil si déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): never
    {
        // Intercepté par le firewall Symfony — ce code n'est jamais exécuté.
        throw new \LogicException('Le firewall intercepte cette route avant son exécution.');
    }

    /**
     * Affiche et traite le formulaire de réinitialisation de mot de passe.
     * Accessible sans connexion (lien reçu par email).
     * Le token expire après 1 heure.
     */
    #[Route('/reinitialisation-mot-de-passe/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
    ): Response {
        $user = $userRepository->findByResetToken($token);

        // Token invalide ou inexistant
        if ($user === null) {
            $this->addFlash('error', 'Ce lien de réinitialisation est invalide ou a déjà été utilisé.');

            return $this->redirectToRoute('app_login');
        }

        // Token expiré (plus d'1 heure)
        $requestedAt = $user->getResetPasswordRequestedAt();
        if ($requestedAt === null || $requestedAt < new \DateTimeImmutable('-1 hour')) {
            $user->setResetPasswordToken(null);
            $user->setResetPasswordRequestedAt(null);
            $em->flush();

            $this->addFlash('error', 'Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau depuis votre profil.');

            return $this->redirectToRoute('app_login');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('reset_password_' . $token, (string) $request->request->get('_csrf_token'))) {
                $errors[] = 'Jeton CSRF invalide. Veuillez réessayer.';
            } else {
                $newPassword     = (string) $request->request->get('new_password', '');
                $confirmPassword = (string) $request->request->get('confirm_password', '');

                if (strlen($newPassword) < 8) {
                    $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
                } elseif (strlen($newPassword) > 64) {
                    $errors[] = 'Le mot de passe ne peut pas dépasser 64 caractères.';
                } elseif ($newPassword !== $confirmPassword) {
                    $errors[] = 'Les deux mots de passe ne correspondent pas.';
                }

                if ($errors === []) {
                    $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                    $user->setResetPasswordToken(null);
                    $user->setResetPasswordRequestedAt(null);
                    $em->flush();

                    $this->addFlash('success', 'Votre mot de passe a été modifié avec succès. Vous pouvez maintenant vous connecter.');

                    return $this->redirectToRoute('app_login');
                }
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'token'  => $token,
            'errors' => $errors,
        ]);
    }
}
