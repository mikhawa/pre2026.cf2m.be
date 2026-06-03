<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
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

class RegistrationController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly UserPasswordHasherInterface $passwordHasher,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
    ) {
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mot de passe placeholder : sera remplacé à l'activation
            $user->setPassword(bin2hex(random_bytes(32)));

            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);
            $user->setStatus(0);

            $em->persist($user);
            $em->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($this->mailFrom, 'CF2m Administration'))
                    ->to(new Address($user->getEmail()))
                    ->subject('Confirmez votre adresse e-mail — CF2m')
                    ->htmlTemplate('emails/registration_confirmation.html.twig')
                    ->context(['user' => $user, 'token' => $token])
            );

            $this->addFlash('success', 'Votre compte a été créé. Vérifiez votre boite mail pour l\'activer.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/inscription/verification/{token}', name: 'app_verify_email')]
    public function verifyEmail(
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $em,
    ): Response {
        $user = $userRepository->findByActivationToken($token);

        if (null === $user) {
            $this->addFlash('error', 'Ce lien de confirmation est invalide ou a déjà été utilisé.');

            return $this->redirectToRoute('app_login');
        }

        $plainPassword = $this->generatePassword();
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
        $user->setStatus(1);
        $user->setActivationToken(null);
        $em->flush();

        $this->mailer->send(
            (new TemplatedEmail())
                ->from(new Address($this->mailFrom, 'CF2m Administration'))
                ->to(new Address($user->getEmail()))
                ->subject('Bienvenue sur CF2m — vos identifiants de connexion')
                ->htmlTemplate('emails/user_bienvenue.html.twig')
                ->context(['user' => $user, 'plainPassword' => $plainPassword])
        );

        $this->addFlash('success', 'Votre compte est activé ! Vos identifiants de connexion vous ont été envoyés par e-mail.');

        return $this->redirectToRoute('app_login');
    }

    private function generatePassword(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*';
        $password = '';
        for ($i = 0; $i < 12; ++$i) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}
