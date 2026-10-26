<?php

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Point d'API public permettant à la SPA React de vérifier que le backend répond.
 * Ne renvoie aucune donnée sensible.
 */
class StatutController extends AbstractController
{
    #[Route('/api/statut', name: 'api_statut', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(['statut' => 'ok']);
    }
}
