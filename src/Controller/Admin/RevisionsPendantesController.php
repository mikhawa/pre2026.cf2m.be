<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Repository\FormationHistoryRepository;
use App\Repository\PageHistoryRepository;
use App\Repository\WorksHistoryRepository;
use App\Service\RevisionService;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur dédié à la page centralisée des révisions en attente.
 * Étend AbstractCrudController pour bénéficier du contexte EasyAdmin (#[AdminRoute]).
 */
class RevisionsPendantesController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        // Requis par AbstractCrudController ; non utilisé dans cette page personnalisée.
        return Formation::class;
    }

    #[AdminRoute(path: '/revisions-en-attente', name: 'revisions_en_attente')]
    #[IsGranted('ROLE_ADMIN')]
    public function revisionsPendantes(
        FormationHistoryRepository $formationHistoryRepo,
        PageHistoryRepository $pageHistoryRepo,
        WorksHistoryRepository $worksHistoryRepo,
        RevisionService $revisionService,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $returnUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('revisionsPendantes')
            ->generateUrl();

        $entries = [];

        // ── Formations ─────────────────────────────────────────────────────
        foreach ($formationHistoryRepo->findAllPending() as $pending) {
            $after       = $revisionService->snapshotFromFormationHistory($pending);
            $prevEntry   = $formationHistoryRepo->findByVersion($pending->getFormation(), $pending->getVersion() - 1);
            $before      = $prevEntry ? $revisionService->snapshotFromFormationHistory($prevEntry) : null;
            $diff        = $revisionService->buildTypedHistoryDiffHtml($after, $before);
            $formationId = $pending->getFormation()->getId();

            $historiqueUrl = $adminUrlGenerator
                ->setController(FormationCrudController::class)
                ->setAction('historiqueFormation')
                ->setEntityId($formationId)
                ->generateUrl();

            $approuverUrl = $adminUrlGenerator
                ->setController(FormationCrudController::class)
                ->setAction('approuverHistoriqueFormation')
                ->setEntityId($formationId)
                ->generateUrl()
                . '?historyId=' . $pending->getId()
                . '&returnUrl=' . urlencode($returnUrl);

            $rejeterUrl = $adminUrlGenerator
                ->setController(FormationCrudController::class)
                ->setAction('rejeterHistoriqueFormation')
                ->setEntityId($formationId)
                ->generateUrl()
                . '?historyId=' . $pending->getId()
                . '&returnUrl=' . urlencode($returnUrl);

            $entries[] = [
                'type'          => 'Formation',
                'title'         => $pending->getTitle(),
                'revision'      => $pending,
                'diff'          => $diff,
                'historiqueUrl' => $historiqueUrl,
                'approuverUrl'  => $approuverUrl,
                'rejeterUrl'    => $rejeterUrl,
            ];
        }

        // ── Pages ──────────────────────────────────────────────────────────
        foreach ($pageHistoryRepo->findAllPending() as $pending) {
            $after     = $revisionService->snapshotFromPageHistory($pending);
            $prevEntry = $pageHistoryRepo->findByVersion($pending->getPage(), $pending->getVersion() - 1);
            $before    = $prevEntry ? $revisionService->snapshotFromPageHistory($prevEntry) : null;
            $diff      = $revisionService->buildTypedHistoryDiffHtml($after, $before);
            $pageId    = $pending->getPage()->getId();

            $historiqueUrl = $adminUrlGenerator
                ->setController(PageCrudController::class)
                ->setAction('historiquePage')
                ->setEntityId($pageId)
                ->generateUrl();

            $approuverUrl = $adminUrlGenerator
                ->setController(PageCrudController::class)
                ->setAction('approuverHistoriquePage')
                ->setEntityId($pageId)
                ->generateUrl()
                . '?historyId=' . $pending->getId()
                . '&returnUrl=' . urlencode($returnUrl);

            $rejeterUrl = $adminUrlGenerator
                ->setController(PageCrudController::class)
                ->setAction('rejeterHistoriquePage')
                ->setEntityId($pageId)
                ->generateUrl()
                . '?historyId=' . $pending->getId()
                . '&returnUrl=' . urlencode($returnUrl);

            $entries[] = [
                'type'          => 'Page',
                'title'         => $pending->getTitle(),
                'revision'      => $pending,
                'diff'          => $diff,
                'historiqueUrl' => $historiqueUrl,
                'approuverUrl'  => $approuverUrl,
                'rejeterUrl'    => $rejeterUrl,
            ];
        }

        // ── Works ──────────────────────────────────────────────────────────
        foreach ($worksHistoryRepo->findAllPending() as $pending) {
            $after     = $revisionService->snapshotFromWorksHistory($pending);
            $prevEntry = $worksHistoryRepo->findByVersion($pending->getWorks(), $pending->getVersion() - 1);
            $before    = $prevEntry ? $revisionService->snapshotFromWorksHistory($prevEntry) : null;
            $diff      = $revisionService->buildTypedHistoryDiffHtml($after, $before);
            $worksId   = $pending->getWorks()->getId();

            $historiqueUrl = $adminUrlGenerator
                ->setController(WorksCrudController::class)
                ->setAction('historiqueWorks')
                ->setEntityId($worksId)
                ->generateUrl();

            $approuverUrl = $adminUrlGenerator
                ->setController(WorksCrudController::class)
                ->setAction('approuverHistoriqueWorks')
                ->setEntityId($worksId)
                ->generateUrl()
                . '?historyId=' . $pending->getId()
                . '&returnUrl=' . urlencode($returnUrl);

            $rejeterUrl = $adminUrlGenerator
                ->setController(WorksCrudController::class)
                ->setAction('rejeterHistoriqueWorks')
                ->setEntityId($worksId)
                ->generateUrl()
                . '?historyId=' . $pending->getId()
                . '&returnUrl=' . urlencode($returnUrl);

            $entries[] = [
                'type'          => 'Works',
                'title'         => $pending->getTitle(),
                'revision'      => $pending,
                'diff'          => $diff,
                'historiqueUrl' => $historiqueUrl,
                'approuverUrl'  => $approuverUrl,
                'rejeterUrl'    => $rejeterUrl,
            ];
        }

        return $this->render('admin/revisions-en-attente.html.twig', [
            'entries' => $entries,
        ]);
    }
}
