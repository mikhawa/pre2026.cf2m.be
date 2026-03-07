<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_SUPER_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
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
        yield MenuItem::linkTo(PageCrudController::class, 'Pages', 'fa fa-file-alt');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users');
        yield MenuItem::linkTo(InscriptionCrudController::class, 'Inscriptions', 'fa fa-clipboard-list');

        yield MenuItem::section('Interactions');
        yield MenuItem::linkTo(CommentCrudController::class, 'Commentaires', 'fa fa-comments');
        yield MenuItem::linkTo(RatingCrudController::class, 'Notes', 'fa fa-star');

        yield MenuItem::section('Communication');
        yield MenuItem::linkTo(ContactMessageCrudController::class, 'Messages de contact', 'fa fa-envelope');
        yield MenuItem::linkTo(PartenaireCrudController::class, 'Partenaires', 'fa fa-handshake');
    }
}
