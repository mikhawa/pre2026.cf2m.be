<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Revision;
use App\Entity\Works;
use App\Repository\RevisionRepository;
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
        private readonly RevisionRepository $revisionRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Works::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $repo = $this->revisionRepository;

        $historique = Action::new('historiqueWorks', 'Historique', 'fa fa-history')
            ->linkToCrudAction('historiqueWorks')
            ->asWarningAction()
            ->setLabel(static function (Works $entity) use ($repo): string {
                return sprintf('Historique (%d)', $repo->countByEntityId('works', $entity->getId()));
            })
        ;

        return $actions
            ->setPermission('historiqueWorks', 'ROLE_ADMIN')
            ->add(Crud::PAGE_INDEX, $historique)
            ->add(Crud::PAGE_EDIT, $historique)
            ->add(Crud::PAGE_DETAIL, $historique)
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, static fn (Action $a) => $a
                ->setLabel('Sauvegarder et continuer les changements')
                ->asWarningAction()
                ->setHtmlAttributes(['data-ea-btn' => 'continue'])
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
        RevisionRepository $revisionRepository,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var Works $works */
        $works = $context->getEntity()->getInstance();
        $worksId = $works->getId();

        $revisions = $revisionRepository->findByEntityId('works', $worksId);

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

        $historique = [];
        foreach ($revisions as $revision) {
            $isCurrent = $revision->getData() === $liveSnapshot;

            $entry = [
                'revision'  => $revision,
                'diff'      => $this->revisionService->buildHistoryDiffHtml($revision),
                'isCurrent' => $isCurrent,
            ];

            if ($revision->getStatus() === Revision::STATUS_PENDING) {
                $entry['approuverUrl'] = $adminUrlGenerator
                    ->setController(WorksRevisionCrudController::class)
                    ->setAction('approuverRevision')
                    ->setEntityId($revision->getId())
                    ->generateUrl() . '?returnUrl=' . urlencode($historiqueUrl);
                $entry['rejeterUrl'] = $adminUrlGenerator
                    ->setController(WorksRevisionCrudController::class)
                    ->setAction('rejeterRevision')
                    ->setEntityId($revision->getId())
                    ->generateUrl() . '?returnUrl=' . urlencode($historiqueUrl);
            }

            if ($revision->getStatus() === Revision::STATUS_APPROVED && !$isCurrent) {
                $entry['appliquerUrl'] = $adminUrlGenerator
                    ->setController(WorksRevisionCrudController::class)
                    ->setAction('appliquerVersion')
                    ->setEntityId($revision->getId())
                    ->generateUrl() . '?returnUrl=' . urlencode($historiqueUrl);
            }

            $historique[] = $entry;
        }

        return $this->render('admin/works/historique.html.twig', [
            'works'      => $works,
            'historique' => $historique,
            'editUrl'    => $editUrl,
        ]);
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
