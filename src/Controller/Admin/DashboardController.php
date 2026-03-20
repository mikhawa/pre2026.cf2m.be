<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\FormationHistoryRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PageHistoryRepository;
use App\Repository\WorksHistoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_FORMATEUR')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly FormationHistoryRepository $formationHistoryRepo,
        private readonly PageHistoryRepository $pageHistoryRepo,
        private readonly WorksHistoryRepository $worksHistoryRepo,
        private readonly InscriptionRepository $inscriptionRepository,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addAssetMapperEntry('admin')
            ->addHtmlContentToHead('<link rel="stylesheet" href="/assets/styles/admin.css">')
        ;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<strong>CF</strong><span style="color:#00b4d8">2m</span> — Administration')
            ->setFaviconPath('images/logo-cf2m.svg')
            ->setTranslationDomain('messages')
            ->renderContentMaximized()
        ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToRoute('← Site public', 'fa fa-arrow-left', 'app_home');

        yield MenuItem::section('Contenu');
        yield MenuItem::linkTo(FormationCrudController::class, 'Formations', 'fa fa-graduation-cap');
        yield MenuItem::linkTo(WorksCrudController::class, 'Works', 'fa fa-folder-open');
        yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file-alt')->setPermission('ROLE_ADMIN');

        yield MenuItem::section('Utilisateurs')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users')->setPermission('ROLE_ADMIN');
        $untreatedCount = $this->inscriptionRepository->findUntreatedCount();
        yield MenuItem::linkTo(InscriptionCrudController::class, 'Inscriptions', 'fa fa-clipboard-list')
            ->setPermission('ROLE_ADMIN')
            ->setBadge($untreatedCount > 0 ? $untreatedCount : null, 'danger')
        ;

        yield MenuItem::section('Interactions');
        yield MenuItem::linkTo(CommentCrudController::class, 'Commentaires', 'fa fa-comments')->setPermission('ROLE_FORMATEUR');
        yield MenuItem::linkTo(RatingCrudController::class, 'Notes', 'fa fa-star')->setPermission('ROLE_ADMIN');
        $pendingCount = $this->formationHistoryRepo->countPending()
            + $this->pageHistoryRepo->countPending()
            + $this->worksHistoryRepo->countPending();
        yield MenuItem::linkTo(FormationCrudController::class, 'Révisions en attente', 'fa fa-history')
            ->setPermission('ROLE_ADMIN')
            ->setBadge($pendingCount > 0 ? $pendingCount : null, 'danger')
        ;

        yield MenuItem::section('Communication');
        yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages de contact', 'fa fa-envelope')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkTo(PartenaireCrudController::class, 'Partenaires', 'fa fa-handshake')->setPermission('ROLE_ADMIN');
    }
}
