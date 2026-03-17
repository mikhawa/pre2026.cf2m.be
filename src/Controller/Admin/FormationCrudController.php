<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Formation;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Field\SunEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class FormationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RevisionService $revisionService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $historique = Action::new('historiqueFormation', 'Historique', 'fa fa-history')
            ->linkToCrudAction('historiqueFormation')
            ->setCssClass('btn btn-outline-info btn-sm')
        ;

        return $actions
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission('historiqueFormation', 'ROLE_ADMIN')
            ->add(Crud::PAGE_INDEX, $historique)
            ->add(Crud::PAGE_EDIT, $historique)
            ->add(Crud::PAGE_DETAIL, $historique)
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Formation')
            ->setEntityLabelInPlural('Formations')
            ->setDefaultSort(['publishedAt' => 'DESC'])
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
                'Brouillon'    => 'draft',
                'Publiée'      => 'published',
                'Archivée'     => 'archived',
                'Recrutement'  => 'recruiting',
            ])
            ->renderAsBadges([
                'draft'      => 'secondary',
                'published'  => 'success',
                'archived'   => 'danger',
                'recruiting' => 'warning',
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
        yield AssociationField::new('createdBy', 'Créée par')
            ->setRequired(true)
        ;
        yield AssociationField::new('responsables', 'Responsables')
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield ColorField::new('colorPrimary', 'Couleur primaire')
            ->hideOnIndex()
            ->setRequired(false)
            ->showValue()
        ;
        yield ColorField::new('colorSecondary', 'Couleur secondaire')
            ->hideOnIndex()
            ->setRequired(false)
            ->showValue()
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
                'Brouillon'   => 'draft',
                'Publiée'     => 'published',
                'Archivée'    => 'archived',
                'Recrutement' => 'recruiting',
            ]))
        ;
    }

    /**
     * Page d'historique des révisions pour une Formation donnée.
     * Affiche toutes les versions sous forme de timeline git-like avec diffs.
     */
    #[AdminRoute(path: '/{entityId}/historique', name: 'historique_formation')]
    public function historiqueFormation(
        AdminContext $context,
        RevisionRepository $revisionRepository,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $formationId = $formation->getId();

        $revisions = $revisionRepository->findByFormationId($formationId);

        // URL de cette page (utilisée comme returnUrl pour revenir après action)
        $historiqueUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('historiqueFormation')
            ->setEntityId($formationId)
            ->generateUrl();

        $editUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($formationId)
            ->generateUrl();

        $historique = [];
        foreach ($revisions as $revision) {
            $entry = [
                'revision' => $revision,
                'diff'     => $this->revisionService->buildHistoryDiffHtml($revision),
            ];

            $baseUrl = $adminUrlGenerator
                ->setController(FormationRevisionCrudController::class)
                ->setEntityId($revision->getId())
                ->generateUrl();

            if ($revision->getStatus() === \App\Entity\Revision::STATUS_PENDING) {
                $entry['approuverUrl'] = $adminUrlGenerator
                    ->setController(FormationRevisionCrudController::class)
                    ->setAction('approuverRevision')
                    ->setEntityId($revision->getId())
                    ->generateUrl() . '?returnUrl=' . urlencode($historiqueUrl);
                $entry['rejeterUrl'] = $adminUrlGenerator
                    ->setController(FormationRevisionCrudController::class)
                    ->setAction('rejeterRevision')
                    ->setEntityId($revision->getId())
                    ->generateUrl() . '?returnUrl=' . urlencode($historiqueUrl);
            }

            if ($revision->getStatus() === \App\Entity\Revision::STATUS_APPROVED) {
                $entry['appliquerUrl'] = $adminUrlGenerator
                    ->setController(FormationRevisionCrudController::class)
                    ->setAction('appliquerVersion')
                    ->setEntityId($revision->getId())
                    ->generateUrl() . '?returnUrl=' . urlencode($historiqueUrl);
            }

            $historique[] = $entry;
        }

        return $this->render('admin/formation/historique.html.twig', [
            'formation'  => $formation,
            'historique' => $historique,
            'editUrl'    => $editUrl,
        ]);
    }

    /**
     * Intercepte la mise à jour pour gérer les révisions.
     * - ROLE_FORMATEUR (sans ROLE_ADMIN) : révision PENDING, contenu live inchangé
     * - ROLE_ADMIN / ROLE_SUPER_ADMIN : révision APPROVED, contenu live mis à jour
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Formation $entityInstance */
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
