<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\FormationHistory;
use App\Entity\PageHistory;
use App\Entity\WorksHistory;
use App\Repository\FormationHistoryRepository;
use App\Repository\PageHistoryRepository;
use App\Repository\WorksHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/preview', name: 'admin_preview_history_')]
class HistoryPreviewController extends AbstractController
{
    public function __construct(
        private readonly FormationHistoryRepository $formationHistoryRepo,
        private readonly PageHistoryRepository $pageHistoryRepo,
        private readonly WorksHistoryRepository $worksHistoryRepo,
    ) {
    }

    #[Route('/formation-history/{id}', name: 'formation', requirements: ['id' => '\d+'])]
    public function previewFormation(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_FORMATEUR');

        $history = $this->formationHistoryRepo->find($id);
        if ($history === null) {
            throw $this->createNotFoundException('Version de formation introuvable.');
        }

        // Un formateur non-admin/pedago ne peut voir que les formations dont il est responsable.
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PEDAGO')) {
            $formation = $history->getFormation();
            if ($formation === null || !$formation->getResponsables()->contains($this->getUser())) {
                throw $this->createAccessDeniedException();
            }
        }

        return $this->render('admin/history_preview/formation.html.twig', [
            'history' => $history,
        ]);
    }

    #[Route('/page-history/{id}', name: 'page', requirements: ['id' => '\d+'])]
    public function previewPage(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $history = $this->pageHistoryRepo->find($id);
        if ($history === null) {
            throw $this->createNotFoundException('Version de page introuvable.');
        }

        return $this->render('admin/history_preview/page.html.twig', [
            'history' => $history,
        ]);
    }

    #[Route('/works-history/{id}', name: 'works', requirements: ['id' => '\d+'])]
    public function previewWorks(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_FORMATEUR');

        $history = $this->worksHistoryRepo->find($id);
        if ($history === null) {
            throw $this->createNotFoundException('Version de works introuvable.');
        }

        // Un formateur non-admin ne peut voir que les works liés à ses formations.
        if (!$this->isGranted('ROLE_ADMIN')) {
            $formation = $history->getFormation();
            if ($formation === null || !$formation->getResponsables()->contains($this->getUser())) {
                throw $this->createAccessDeniedException();
            }
        }

        return $this->render('admin/history_preview/works.html.twig', [
            'history' => $history,
        ]);
    }
}
