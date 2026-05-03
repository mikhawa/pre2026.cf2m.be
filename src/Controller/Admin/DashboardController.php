<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ContactMessageRepository;
use App\Repository\FormationHistoryRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PageHistoryRepository;
use App\Repository\WorksHistoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_STAGIAIRE')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly FormationHistoryRepository $formationHistoryRepo,
        private readonly PageHistoryRepository $pageHistoryRepo,
        private readonly WorksHistoryRepository $worksHistoryRepo,
        private readonly InscriptionRepository $inscriptionRepository,
        private readonly ContactMessageRepository $contactMessageRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    /**
     * Route de redirection pour que path('admin_revisions_en_attente') fonctionne
     * dans les templates Twig (ex. profil/index.html.twig).
     * Redirige vers la page EasyAdmin dédiée (RevisionsPendantesController).
     */
    #[Route('/admin/revisions-en-attente', name: 'admin_revisions_en_attente')]
    #[IsGranted('ROLE_ADMIN')]
    public function revisionsPendantesRedirect(): Response
    {
        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(RevisionsPendantesController::class)
                ->setAction('revisionsPendantes')
                ->generateUrl()
        );
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToRoute('Site public', 'fa fa-globe', 'app_home'),
                MenuItem::linkToRoute('Mon profil', 'fa fa-user', 'app_profile'),
            ])
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addAssetMapperEntry('admin')
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
        $pendingFormations = $this->formationHistoryRepo->countPending();
        $pendingWorks      = $this->worksHistoryRepo->countPending();
        $pendingPages      = $this->pageHistoryRepo->countPending();
        $totalPending      = $pendingFormations + $pendingWorks + $pendingPages;

        $revisionsPendantesUrl = $this->adminUrlGenerator
            ->setController(RevisionsPendantesController::class)
            ->setAction('revisionsPendantes')
            ->generateUrl();

        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToRoute('← Site public', 'fa fa-arrow-left', 'app_home');

        yield MenuItem::section('Contenu');
        yield MenuItem::linkTo(FormationCrudController::class, 'Formations', 'fa fa-graduation-cap')
            ->setPermission('ROLE_FORMATEUR')
            ->setBadge($pendingFormations > 0 ? $pendingFormations : null, 'danger')
        ;
        yield MenuItem::linkTo(WorksCrudController::class, 'Works', 'fa fa-folder-open')
            ->setBadge($pendingWorks > 0 ? $pendingWorks : null, 'danger')
        ;
        yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file-alt')
            ->setPermission('CONTENT_MANAGER')
            ->setBadge($pendingPages > 0 ? $pendingPages : null, 'danger')
        ;

        yield MenuItem::section('Utilisateurs')->setPermission('CONTENT_MANAGER');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users')->setPermission('CONTENT_MANAGER');
        $untreatedCount = $this->inscriptionRepository->findUntreatedCount();
        yield MenuItem::linkTo(InscriptionCrudController::class, 'Inscriptions', 'fa fa-clipboard-list')
            ->setPermission('CONTENT_MANAGER')
            ->setBadge($untreatedCount > 0 ? $untreatedCount : null, 'danger')
        ;

        yield MenuItem::section('Interactions');
        yield MenuItem::linkTo(CommentCrudController::class, 'Commentaires', 'fa fa-comments')->setPermission('ROLE_STAGIAIRE');
        yield MenuItem::linkTo(RatingCrudController::class, 'Notes', 'fa fa-star')->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('Révisions en attente', 'fa fa-clock', $revisionsPendantesUrl)
            ->setPermission('ROLE_ADMIN')
            ->setBadge($totalPending > 0 ? $totalPending : null, 'danger')
        ;

        $unreadMessages = $this->contactMessageRepository->countUnread();

        yield MenuItem::section('Communication');
        yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages de contact', 'fa fa-envelope')
            ->setPermission('CONTENT_MANAGER')
            ->setBadge($unreadMessages > 0 ? $unreadMessages : null, 'danger')
        ;
        yield MenuItem::linkTo(PartenaireCrudController::class, 'Partenaires', 'fa fa-handshake')->setPermission('ROLE_ADMIN');
    }
}
