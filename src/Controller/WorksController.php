<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\WorksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorksController extends AbstractController
{
    #[Route('/formation/{formationSlug}/works/{slug}', name: 'app_works_show')]
    public function show(string $formationSlug, string $slug, WorksRepository $worksRepo): Response
    {
        $work = $worksRepo->findOnePublishedBySlugAndFormation($slug, $formationSlug);

        if (!$work) {
            throw $this->createNotFoundException('Réalisation introuvable.');
        }

        return $this->render('works/show.html.twig', [
            'work' => $work,
        ]);
    }
}
