<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Works;
use App\Entity\WorksHistory;
use App\Repository\WorksHistoryRepository;
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

class WorksCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RevisionService $revisionService,
        private readonly WorksHistoryRepository $worksHistoryRepo,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Works::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $worksHistoryRepo = $this->worksHistoryRepo;

        $historique = Action::new('historiqueWorks', 'Historique', 'fa fa-history')
            ->linkToCrudAction('historiqueWorks')
            ->asWarningAction()
            ->setLabel(static function (Works $entity) use ($worksHistoryRepo): string {
                return sprintf('Historique (%d)', count($worksHistoryRepo->findHistoryForWorks($entity)));
            })
        ;

        return $actions
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission('historiqueWorks', 'ROLE_ADMIN')
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
            ->reorder(Crud::PAGE_EDIT, [Action::SAVE_AND_RETURN, Action::SAVE_AND_CONTINUE, 'historiqueWorks'])
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Work')
            ->setEntityLabelInPlural('Works')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['title', 'slug'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextField::new('slug', 'Slug');
        yield AssociationField::new('formation', 'Formation');
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Brouillon'  => 'draft',
                'Publié'     => 'published',
                'Archivé'    => 'archived',
            ])
            ->renderAsBadges([
                'draft'     => 'secondary',
                'published' => 'success',
                'archived'  => 'danger',
            ])
        ;
        yield DateTimeField::new('publishedAt', 'Publié le')
            ->setFormat('dd/MM/yyyy')
            ->setRequired(false)
        ;
        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm')
        ;
        yield AssociationField::new('users', 'Étudiants')
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield SunEditorField::new('description', 'Description')
            ->hideOnIndex()
            ->setRequired(false)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                'Brouillon'  => 'draft',
                'Publié'     => 'published',
                'Archivé'    => 'archived',
            ]))
        ;
    }

    /**
     * Page d'historique des révisions pour un Works donné.
     */
    #[AdminRoute(path: '/{entityId}/historique', name: 'historique_works')]
    public function historiqueWorks(
        AdminContext $context,
        WorksHistoryRepository $worksHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var Works $works */
        $works   = $context->getEntity()->getInstance();
        $worksId = $works->getId();

        $entries = $worksHistoryRepo->findHistoryForWorks($works);

        $historiqueUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('historiqueWorks')
            ->setEntityId($worksId)
            ->generateUrl();

        $editUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($worksId)
            ->generateUrl();

        $liveSnapshot = $this->revisionService->getLiveWorksSnapshot($works);

        // Pré-calcul des snapshots pour chaque entrée
        $snapshots = [];
        foreach ($entries as $i => $entry) {
            $snapshots[$i] = $this->revisionService->snapshotFromWorksHistory($entry);
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

            if ($entry->getRevisionStatus() === WorksHistory::STATUS_PENDING) {
                $histEntry['approuverUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('approuverHistoriqueWorks')
                    ->setEntityId($worksId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId()
                    . '&returnUrl=' . urlencode($historiqueUrl);
                $histEntry['rejeterUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('rejeterHistoriqueWorks')
                    ->setEntityId($worksId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId()
                    . '&returnUrl=' . urlencode($historiqueUrl);
            }

            $historique[] = $histEntry;
        }

        return $this->render('admin/works/historique.html.twig', [
            'works'      => $works,
            'historique' => $historique,
            'editUrl'    => $editUrl,
        ]);
    }

    /**
     * Approuve et applique une version de l'historique typé Works.
     */
    #[AdminRoute(path: '/{entityId}/historique/approuver', name: 'approuver_historique_works')]
    public function approuverHistoriqueWorks(
        AdminContext $context,
        WorksHistoryRepository $worksHistoryRepo,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $worksHistoryRepo->find($historyId);

        if (!$history || $history->getRevisionStatus() !== WorksHistory::STATUS_PENDING) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->approuverWorksHistory($history, $reviewer);
        $this->revisionService->notifyAuthorFromHistory($history, true);

        $this->addFlash('success', sprintf('La révision de « %s » a été approuvée et appliquée.', $history->getTitle()));

        $returnUrl = $context->getRequest()->query->get('returnUrl');

        return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
    }

    /**
     * Rejette une version de l'historique typé Works.
     */
    #[AdminRoute(path: '/{entityId}/historique/rejeter', name: 'rejeter_historique_works')]
    public function rejeterHistoriqueWorks(
        AdminContext $context,
        WorksHistoryRepository $worksHistoryRepo,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $worksHistoryRepo->find($historyId);

        if (!$history || $history->getRevisionStatus() !== WorksHistory::STATUS_PENDING) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->rejeterWorksHistory($history, $reviewer);
        $this->revisionService->notifyAuthorFromHistory($history, false);

        $this->addFlash('info', sprintf('La révision de « %s » a été rejetée.', $history->getTitle()));

        $returnUrl = $context->getRequest()->query->get('returnUrl');

        return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
    }

    /**
     * Intercepte la mise à jour pour créer une révision.
     * Works : toujours auto-approuvée, contenu live mis à jour normalement.
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Works $entityInstance */
        $user = $this->getUser();

        $this->revisionService->createRevision($entityInstance, $user, true);
        parent::updateEntity($entityManager, $entityInstance);
    }
}
