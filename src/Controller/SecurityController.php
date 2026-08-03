<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\TurnstileVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
        private readonly TurnstileVerifier $turnstile,
    ) {
    }

    #[Route('/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirige vers le profil si déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Formulaire de demande de réinitialisation de mot de passe (utilisateur non connecté).
     * Envoie toujours un message générique, que l'e-mail corresponde ou non à un compte,
     * afin de ne pas révéler l'existence d'un compte à cette adresse.
     */
    #[Route('/mot-de-passe-oublie', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $error = null;

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('forgot_password', (string) $request->request->get('_csrf_token'))) {
                $error = 'Jeton CSRF invalide. Veuillez réessayer.';
            } else {
                $email = trim((string) $request->request->get('email', ''));

                if ('' === $email) {
                    $error = 'Veuillez indiquer votre adresse e-mail.';
                } else {
                    $token = (string) $request->request->get('cf-turnstile-response', '');
                    if (!$this->turnstile->verify($token, $request->getClientIp())) {
                        $error = 'La vérification anti-robot a échoué. Veuillez réessayer.';
                    } else {
                        $user = $userRepository->findOneBy(['email' => $email]);

                        if (null !== $user) {
                            $resetToken = bin2hex(random_bytes(32));
                            $user->setResetPasswordToken($resetToken);
                            $user->setResetPasswordRequestedAt(new \DateTimeImmutable());
                            $em->flush();

                            $this->mailer->send(
                                (new TemplatedEmail())
                                    ->from(new Address($this->mailFrom, 'CF2m Administration'))
                                    ->to(new Address($user->getEmail()))
                                    ->subject('Réinitialisation de votre mot de passe — CF2m')
                                    ->htmlTemplate('emails/reset_password.html.twig')
                                    ->context(['user' => $user, 'token' => $resetToken])
                            );
                        }

                        $this->addFlash('success', 'Si un compte existe avec cette adresse e-mail, un lien de réinitialisation vient de lui être envoyé. Il est valable 1 heure.');

                        return $this->redirectToRoute('app_login');
                    }
                }
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'error' => $error,
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
        if (null === $user) {
            $this->addFlash('error', 'Ce lien de réinitialisation est invalide ou a déjà été utilisé.');

            return $this->redirectToRoute('app_login');
        }

        // Token expiré (plus d'1 heure)
        $requestedAt = $user->getResetPasswordRequestedAt();
        if (null === $requestedAt || $requestedAt < new \DateTimeImmutable('-1 hour')) {
            $user->setResetPasswordToken(null);
            $user->setResetPasswordRequestedAt(null);
            $em->flush();

            $this->addFlash('error', 'Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau depuis votre profil.');

            return $this->redirectToRoute('app_login');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('reset_password_'.$token, (string) $request->request->get('_csrf_token'))) {
                $errors[] = 'Jeton CSRF invalide. Veuillez réessayer.';
            } else {
                $newPassword = (string) $request->request->get('new_password', '');
                $confirmPassword = (string) $request->request->get('confirm_password', '');

                if (strlen($newPassword) < 8) {
                    $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
                } elseif (strlen($newPassword) > 64) {
                    $errors[] = 'Le mot de passe ne peut pas dépasser 64 caractères.';
                } elseif ($newPassword !== $confirmPassword) {
                    $errors[] = 'Les deux mots de passe ne correspondent pas.';
                }

                if ([] === $errors) {
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
            'token' => $token,
            'errors' => $errors,
        ]);
    }
}
