<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\FormationRepository;
use App\Repository\PartenaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        FormationRepository $formationRepo,
        PartenaireRepository $partenaireRepo,
    ): Response {
        return $this->render('home/index.html.twig', [
            'formations'  => $formationRepo->findAllPublished(),
            'partenaires' => $partenaireRepo->findAllActive(),
        ]);
    }
}
