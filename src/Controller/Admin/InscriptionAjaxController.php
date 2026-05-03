<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\InscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class InscriptionAjaxController extends AbstractController
{
    public function __construct(
        private readonly InscriptionRepository $inscriptionRepository,
    ) {}

    #[Route('/admin/inscription/{id}/traitement-info', name: 'admin_inscription_traitement_info', methods: ['GET'])]
    public function traitementInfo(int $id): JsonResponse
    {
        $inscription = $this->inscriptionRepository->find($id);

        if (!$inscription) {
            return $this->json(['error' => 'Inscription introuvable'], 404);
        }

        $treatAt = $inscription->getTreatAt();
        $treatBy = $inscription->getTreatBy();

        return $this->json([
            'treatAt'        => $treatAt?->format('d/m/Y H:i') ?? '',
            'treatAtIso'     => $treatAt?->format('c') ?? '',
            'treatBy'        => $treatBy ? (string) $treatBy : '',
            'untreatedCount' => $this->inscriptionRepository->findUntreatedCount(),
        ]);
    }
}
