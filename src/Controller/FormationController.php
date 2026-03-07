<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formation', name: 'app_formation_')]
class FormationController extends AbstractController
{
    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, FormationRepository $formationRepo): Response
    {
        $formation = $formationRepo->findOneBySlug($slug);

        if ($formation === null || $formation->getStatus() !== 'published') {
            throw $this->createNotFoundException('Formation introuvable.');
        }

        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }
}
