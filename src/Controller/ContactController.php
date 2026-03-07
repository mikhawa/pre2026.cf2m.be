<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ContactMessage;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $message = new ContactMessage();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si le champ honeypot est rempli, on simule un succès sans rien enregistrer
            $honeypot = $form->get('url')->getData();
            if (!empty($honeypot)) {
                return $this->redirectToRoute('app_contact_success');
            }

            $em->persist($message);
            $em->flush();

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
