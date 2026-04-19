<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
    ) {
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $token = bin2hex(random_bytes(32));
            $user->setActivationToken($token);
            $user->setStatus(0);

            $em->persist($user);
            $em->flush();

            $email = (new TemplatedEmail())
                ->from(new Address($this->mailFrom, 'CF2m Administration'))
                ->to(new Address($user->getEmail()))
                ->subject('Confirmez votre adresse e-mail — CF2m')
                ->htmlTemplate('emails/registration_confirmation.html.twig')
                ->context(['user' => $user, 'token' => $token]);

            $this->mailer->send($email);

            $this->addFlash('success', 'Votre compte a été créé. Vérifiez votre boite mail pour activer votre compte.');

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
        Security $security,
    ): Response {
        $user = $userRepository->findByActivationToken($token);

        if ($user === null) {
            $this->addFlash('error', 'Ce lien de confirmation est invalide ou a déjà été utilisé.');

            return $this->redirectToRoute('app_login');
        }

        $user->setStatus(1);
        $user->setActivationToken(null);
        $em->flush();

        $response = $security->login($user, 'form_login', 'main');

        $this->addFlash('success', 'Votre compte est activé. Bienvenue sur CF2m !');

        return $response ?? $this->redirectToRoute('app_profile');
    }
}
