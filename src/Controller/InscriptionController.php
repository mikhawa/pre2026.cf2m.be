<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\Admin\InscriptionCrudController;
use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\FormationRepository;
use App\Repository\UserRepository;
use App\Repository\WorksRepository;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/preinscription', name: 'app_inscription_')]
class InscriptionController extends AbstractController
{
    public function __construct(
        #[Autowire(env: 'MAIL_ADMIN')]
        private readonly string $mailAdmin,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailForm,
        private readonly TurnstileVerifier $turnstile,
    ) {
    }

    #[Route('/{formationSlug}', name: 'create', methods: ['POST'])]
    public function create(
        string $formationSlug,
        Request $request,
        FormationRepository $formationRepo,
        WorksRepository $worksRepo,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        MailerInterface $mailer,
    ): Response {
        $formation = $formationRepo->findOneBySlug($formationSlug);

        if (null === $formation || 'recruiting' !== $formation->getStatus()) {
            throw $this->createNotFoundException('Formation introuvable ou non ouverte au recrutement.');
        }

        $inscription = new Inscription();
        $inscription->setFormation($formation);

        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérification Cloudflare Turnstile
            $token = (string) $request->request->get('cf-turnstile-response', '');
            if (!$this->turnstile->verify($token, $request->getClientIp())) {
                $works = $worksRepo->findPublishedByFormation($formation->getId());

                return $this->render('formation/show.html.twig', [
                    'formation' => $formation,
                    'works' => $works,
                    'inscriptionForm' => $form,
                    'showInscriptionModal' => true,
                    'turnstileError' => 'La vérification anti-robot a échoué. Veuillez réessayer.',
                ]);
            }

            $em->persist($inscription);
            $em->flush();

            // Notification aux administrateurs et pédagos
            $admins = $userRepo->findInscriptionRecipients();
            $adminListUrl = $this->generateUrl('admin', [
                'crudAction' => 'index',
                'crudControllerFqcn' => InscriptionCrudController::class,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            foreach ($admins as $admin) {
                $emailAdmin = (new TemplatedEmail())
                    ->from(new Address($this->mailForm, 'CF2m — Préinscriptions'))
                    ->to(new Address($admin->getEmail(), (string) $admin))
                    ->replyTo(new Address($inscription->getEmail(), $inscription->getPrenom().' '.$inscription->getNom()))
                    ->subject('[CF2m] Nouvelle préinscription — '.$formation->getTitle())
                    ->htmlTemplate('emails/inscription_admin.html.twig')
                    ->context([
                        'inscription' => $inscription,
                        'formation' => $formation,
                        'adminListUrl' => $adminListUrl,
                    ])
                ;
                $mailer->send($emailAdmin);
            }

            // Accusé de réception à la personne
            $emailAr = (new TemplatedEmail())
                ->from(new Address($this->mailForm, 'CF2m — Centre de Formation'))
                ->to(new Address($inscription->getEmail(), $inscription->getPrenom().' '.$inscription->getNom()))
                ->subject('[CF2m] Votre demande de préinscription — '.$formation->getTitle())
                ->htmlTemplate('emails/inscription_confirmation.html.twig')
                ->context([
                    'inscription' => $inscription,
                    'formation' => $formation,
                ])
            ;
            $mailer->send($emailAr);

            $this->addFlash('inscription_success', '1');

            return $this->redirectToRoute('app_formation_show', ['slug' => $formationSlug]);
        }

        // En cas d'erreur de validation, ré-afficher la page avec le formulaire et la modale ouverte
        $works = $worksRepo->findPublishedByFormation($formation->getId());

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
            'works' => $works,
            'inscriptionForm' => $form,
            'showInscriptionModal' => true,
        ]);
    }
}
