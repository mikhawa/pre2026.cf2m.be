<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil', name: 'app_profile')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
    ) {
    }
    #[Route('', name: '')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * Génère un token de réinitialisation et envoie le lien par email à l'utilisateur connecté.
     */
    #[Route('/demande-reinitialisation', name: '_request_reset', methods: ['POST'])]
    public function requestPasswordReset(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $token = bin2hex(random_bytes(32)); // 64 caractères hexadécimaux
        $user->setResetPasswordToken($token);
        $user->setResetPasswordRequestedAt(new \DateTimeImmutable());
        $em->flush();

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailFrom, 'CF2m Administration'))
            ->to(new Address($user->getEmail()))
            ->subject('Réinitialisation de votre mot de passe — CF2m')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context(['user' => $user, 'token' => $token]);

        $this->mailer->send($email);

        $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse e-mail. Il est valable 1 heure.');

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/modifier', name: '_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            // Vich laisse l'objet File sur l'entité après le flush ; on le retire
            // avant que Symfony sérialise la session pour éviter l'erreur
            // "Serialization of File is not allowed"
            $user->setAvatarFile(null);
            $this->addFlash('success', 'Votre profil a été mis à jour.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
