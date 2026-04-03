<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\FormationRepository;
use App\Repository\WorksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formation', name: 'app_formation_')]
class FormationController extends AbstractController
{
    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, FormationRepository $formationRepo, WorksRepository $worksRepo): Response
    {
        $formation = $formationRepo->findOneBySlug($slug);

        if ($formation === null || !in_array($formation->getStatus(), ['published', 'recruiting'], true)) {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        $works = $worksRepo->findPublishedByFormation($formation->getId());

        $inscriptionForm = null;
        if ($formation->getStatus() === 'recruiting') {
            $inscriptionForm = $this->createForm(InscriptionType::class, new Inscription());
        }

        return $this->render('formation/show.html.twig', [
            'formation'       => $formation,
            'works'           => $works,
            'inscriptionForm' => $inscriptionForm,
        ]);
    }
}
