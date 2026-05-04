<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ContactMessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ContactMessageAjaxController extends AbstractController
{
    public function __construct(
        private readonly ContactMessageRepository $contactMessageRepository,
    ) {}

    #[Route('/admin/contact-message/{id}/lecture-info', name: 'admin_contact_message_lecture_info', methods: ['GET'])]
    public function lectureInfo(int $id): JsonResponse
    {
        $message = $this->contactMessageRepository->find($id);

        if (!$message) {
            return $this->json(['error' => 'Message introuvable'], 404);
        }

        $readBy = $message->getReadBy();

        return $this->json([
            'readBy'      => $readBy ? (string) $readBy : '',
            'unreadCount' => $this->contactMessageRepository->countUnread(),
        ]);
    }
}
