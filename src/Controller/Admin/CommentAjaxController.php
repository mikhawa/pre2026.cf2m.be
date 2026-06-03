<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_FORMATEUR')]
class CommentAjaxController extends AbstractController
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
    ) {
    }

    #[Route('/admin/comment/{id}/approbation-info', name: 'admin_comment_approbation_info', methods: ['GET'])]
    public function approbationInfo(int $id): JsonResponse
    {
        $comment = $this->commentRepository->find($id);

        if (!$comment) {
            return $this->json(['error' => 'Commentaire introuvable'], 404);
        }

        $approvedBy = $comment->getApprovedBy();
        $approvedAt = $comment->getApprovedAt();

        return $this->json([
            'approvedBy' => $approvedBy ? (string) $approvedBy : '',
            'approvedAt' => $approvedAt?->format('d/m/Y H:i') ?? '',
            'approvedAtIso' => $approvedAt?->format('c') ?? '',
            'unapprovedCount' => $this->commentRepository->countUnapproved(),
        ]);
    }
}
