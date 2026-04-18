<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublicProfileController extends AbstractController
{
    #[Route('/utilisateur/{id}', name: 'app_public_profile', requirements: ['id' => '\d+'])]
    public function show(User $user): Response
    {
        return $this->render('profil/public.html.twig', [
            'user' => $user,
        ]);
    }
}
