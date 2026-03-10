<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactType;
use App\Service\TurnstileVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'MAIL_ADMIN')]
        private readonly string $mailAdmin,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailForm,
        private readonly TurnstileVerifier $turnstile,
    ) {
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
    ): Response {
        $message = new ContactMessage();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le champ honeypot est rempli, on simule un succès sans rien enregistrer
            if (!empty($form->get('url')->getData())) {
                return $this->redirectToRoute('app_contact_success');
            }

            // Vérification Cloudflare Turnstile
            $token = (string) $request->request->get('cf-turnstile-response', '');
            if (!$this->turnstile->verify($token, $request->getClientIp())) {
                $this->addFlash('error', 'La vérification anti-robot a échoué. Veuillez réessayer.');

                return $this->render('contact/index.html.twig', ['form' => $form]);
            }

            $em->persist($message);
            $em->flush();

            $email = (new TemplatedEmail())
                ->from(new Address($this->mailForm, 'CF2m — Contact'))
                ->to($this->mailAdmin)
                ->replyTo(new Address($message->getEmail(), $message->getNom()))
                ->subject('[CF2m] ' . $message->getSujet())
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['message' => $message])
            ;

            $mailer->send($email);

            return $this->redirectToRoute('app_contact_success');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/contact/merci', name: 'app_contact_success')]
    public function success(): Response
    {
        return $this->render('contact/success.html.twig');
    }
}
