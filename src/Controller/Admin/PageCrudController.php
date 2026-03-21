<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\PageHistory;
use App\Repository\PageHistoryRepository;
use App\Service\RevisionService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Field\SunEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class PageCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RevisionService $revisionService,
        private readonly PageHistoryRepository $pageHistoryRepo,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $pageHistoryRepo = $this->pageHistoryRepo;

        $historique = Action::new('historiquePage', 'Historique', 'fa fa-history')
            ->linkToCrudAction('historiquePage')
            ->asWarningAction()
            ->setLabel(static function (Page $entity) use ($pageHistoryRepo): string {
                return sprintf('Historique (%d)', count($pageHistoryRepo->findHistoryForPage($entity)));
            })
        ;

        return $actions
            ->setPermission(Action::INDEX, 'ROLE_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DETAIL, 'ROLE_ADMIN')
            ->setPermission('historiquePage', 'ROLE_ADMIN')
            ->add(Crud::PAGE_INDEX, $historique)
            ->add(Crud::PAGE_EDIT, $historique)
            ->add(Crud::PAGE_DETAIL, $historique)
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, static fn (Action $a) => $a
                ->setLabel('Sauvegarder et continuer les changements')
                ->asWarningAction()
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, static fn (Action $a) => $a
                ->asSuccessAction()
            )
            ->reorder(Crud::PAGE_EDIT, [Action::SAVE_AND_RETURN, Action::SAVE_AND_CONTINUE, 'historiquePage'])
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page')
            ->setEntityLabelInPlural('Pages')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['title', 'slug'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextField::new('slug', 'Slug');
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Brouillon'  => 'draft',
                'Publiée'    => 'published',
                'Archivée'   => 'archived',
            ])
            ->renderAsBadges([
                'draft'     => 'secondary',
                'published' => 'success',
                'archived'  => 'danger',
            ])
        ;
        yield DateTimeField::new('publishedAt', 'Publiée le')
            ->setFormat('dd/MM/yyyy')
            ->setRequired(false)
        ;
        yield DateTimeField::new('createdAt', 'Créée le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm')
        ;
        yield AssociationField::new('users', 'Auteurs')
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield SunEditorField::new('content', 'Contenu')
            ->hideOnIndex()
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                'Brouillon'  => 'draft',
                'Publiée'    => 'published',
                'Archivée'   => 'archived',
            ]))
        ;
    }

    /**
     * Page d'historique des révisions pour une Page donnée.
     */
    #[AdminRoute(path: '/{entityId}/historique', name: 'historique_page')]
    public function historiquePage(
        AdminContext $context,
        PageHistoryRepository $pageHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var Page $page */
        $page   = $context->getEntity()->getInstance();
        $pageId = $page->getId();

        $entries = $pageHistoryRepo->findHistoryForPage($page);

        $historiqueUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('historiquePage')
            ->setEntityId($pageId)
            ->generateUrl();

        $editUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($pageId)
            ->generateUrl();

        $liveSnapshot = $this->revisionService->getLivePageSnapshot($page);

        // Pré-calcul des snapshots pour chaque entrée
        $snapshots = [];
        foreach ($entries as $i => $entry) {
            $snapshots[$i] = $this->revisionService->snapshotFromPageHistory($entry);
        }

        $historique = [];
        foreach ($entries as $i => $entry) {
            $isCurrent = ($snapshots[$i] === $liveSnapshot);

            $diff = $this->revisionService->buildTypedHistoryDiffHtml(
                $snapshots[$i],
                $snapshots[$i + 1] ?? null
            );

            $histEntry = [
                'revision'  => $entry,
                'diff'      => $diff,
                'isCurrent' => $isCurrent,
            ];

            if ($entry->getRevisionStatus() === PageHistory::STATUS_PENDING) {
                $histEntry['approuverUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('approuverHistoriquePage')
                    ->setEntityId($pageId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId()
                    . '&returnUrl=' . urlencode($historiqueUrl);
                $histEntry['rejeterUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('rejeterHistoriquePage')
                    ->setEntityId($pageId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId()
                    . '&returnUrl=' . urlencode($historiqueUrl);
            }

            $restaurable = [PageHistory::STATUS_APPROVED, PageHistory::STATUS_AUTO_APPROVED];
            if (!$isCurrent && in_array($entry->getRevisionStatus(), $restaurable, true)) {
                $histEntry['restaurerUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('restaurerHistoriquePage')
                    ->setEntityId($pageId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId();
            }

            $historique[] = $histEntry;
        }

        return $this->render('admin/page/historique.html.twig', [
            'page'       => $page,
            'historique' => $historique,
            'editUrl'    => $editUrl,
        ]);
    }

    /**
     * Approuve et applique une version de l'historique typé Page.
     */
    #[AdminRoute(path: '/{entityId}/historique/approuver', name: 'approuver_historique_page')]
    public function approuverHistoriquePage(
        AdminContext $context,
        PageHistoryRepository $pageHistoryRepo,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $pageHistoryRepo->find($historyId);

        if (!$history || $history->getRevisionStatus() !== PageHistory::STATUS_PENDING) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->approuverPageHistory($history, $reviewer);
        $this->revisionService->notifyAuthorFromHistory($history, true);

        $this->addFlash('success', sprintf('La révision de « %s » a été approuvée et appliquée.', $history->getTitle()));

        $returnUrl = $context->getRequest()->query->get('returnUrl');

        return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
    }

    /**
     * Rejette une version de l'historique typé Page.
     */
    #[AdminRoute(path: '/{entityId}/historique/rejeter', name: 'rejeter_historique_page')]
    public function rejeterHistoriquePage(
        AdminContext $context,
        PageHistoryRepository $pageHistoryRepo,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $pageHistoryRepo->find($historyId);

        if (!$history || $history->getRevisionStatus() !== PageHistory::STATUS_PENDING) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->rejeterPageHistory($history, $reviewer);
        $this->revisionService->notifyAuthorFromHistory($history, false);

        $this->addFlash('info', sprintf('La révision de « %s » a été rejetée.', $history->getTitle()));

        $returnUrl = $context->getRequest()->query->get('returnUrl');

        return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
    }

    /**
     * Restaure une version de l'historique Page sur l'entité live.
     */
    #[AdminRoute(path: '/{entityId}/historique/restaurer', name: 'restaurer_historique_page')]
    public function restaurerHistoriquePage(
        AdminContext $context,
        PageHistoryRepository $pageHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $pageHistoryRepo->find($historyId);
        $pageId    = $context->getEntity()->getInstance()?->getId();

        $returnUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('historiquePage')
            ->setEntityId($pageId)
            ->generateUrl();

        $restaurable = [PageHistory::STATUS_APPROVED, PageHistory::STATUS_AUTO_APPROVED];
        if (!$history || !in_array($history->getRevisionStatus(), $restaurable, true)) {
            $this->addFlash('danger', 'Version introuvable ou non restaurable.');

            return $this->redirect($returnUrl);
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->restaurerPageHistory($history, $reviewer);

        $this->addFlash('success', sprintf('La version v%d de « %s » a été restaurée.', $history->getVersion(), $history->getTitle()));

        return $this->redirect($returnUrl);
    }

    /**
     * Intercepte la mise à jour pour gérer les révisions.
     * - ROLE_FORMATEUR (sans ROLE_ADMIN) : révision PENDING, contenu live inchangé
     * - ROLE_ADMIN / ROLE_SUPER_ADMIN : révision APPROVED, contenu live mis à jour
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Page $entityInstance */
        $user = $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        if (!$isAdmin) {
            // Formateur : créer une révision PENDING et annuler la modification live
            $revision = $this->revisionService->createRevision($entityInstance, $user, false);
            $entityManager->refresh($entityInstance);
            $entityManager->flush();
            $this->revisionService->notifyAdmins($revision);
            $this->addFlash('warning', 'Votre modification a été soumise pour validation par un administrateur.');

            return;
        }

        // Admin/Super-admin : créer une révision APPROVED et sauvegarder normalement
        $this->revisionService->createRevision($entityInstance, $user, true);
        parent::updateEntity($entityManager, $entityInstance);
    }
}
