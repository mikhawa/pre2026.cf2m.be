<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Entity\FormationHistory;
use App\Repository\FormationHistoryRepository;
use App\Service\RevisionService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use App\Field\SunEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class FormationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RevisionService $revisionService,
        private readonly FormationHistoryRepository $formationHistoryRepo,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Formation::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $formationHistoryRepo = $this->formationHistoryRepo;

        $historique = Action::new('historiqueFormation', 'Historique', 'fa fa-history')
            ->linkToCrudAction('historiqueFormation')
            ->asWarningAction()
            ->setLabel(static function (Formation $entity) use ($formationHistoryRepo): string {
                return sprintf('Historique (%d)', count($formationHistoryRepo->findHistoryForFormation($entity)));
            })
        ;

        return $actions
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission('historiqueFormation', 'ROLE_ADMIN')
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
            ->reorder(Crud::PAGE_EDIT, [Action::SAVE_AND_RETURN, Action::SAVE_AND_CONTINUE, 'historiqueFormation'])
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
        $formationHistoryRepo = $this->formationHistoryRepo;
        yield TextField::new('revisionPendante', ' ')
            ->onlyOnIndex()
            ->renderAsHtml()
            ->setSortable(false)
            ->formatValue(static function (mixed $value, ?Formation $entity) use ($formationHistoryRepo): string {
                if ($entity === null) {
                    return '';
                }
                $pending = $formationHistoryRepo->findPendingForFormation($entity);

                return $pending !== null
                    ? '<span class="badge text-bg-warning text-nowrap"><i class="fa fa-clock me-1"></i>En attente</span>'
                    : '';
            })
        ;
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
            ->hideWhenCreating()
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
        yield TextareaField::new('descriptionCourte', 'Description courte (accueil, max 800 car.)')
            ->hideOnIndex()
            ->setRequired(false)
            ->setHelp('Texte affiché sur la page d\'accueil à la place de la description longue. 800 caractères maximum.')
        ;
        yield Field::new('logoFile', 'Logo')
            ->setFormType(VichImageType::class)
            ->setFormTypeOptions(['allow_delete' => true, 'download_uri' => false])
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield ImageField::new('logo', 'Logo')
            ->setBasePath('/uploads/formation-logos/')
            ->hideOnForm()
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
                'Brouillon'   => 'draft',
                'Publiée'     => 'published',
                'Archivée'    => 'archived',
                'Recrutement' => 'recruiting',
            ]))
        ;
    }

    /**
     * Surcharge l'action edit pour pré-remplir le formulaire avec les données de la révision PENDING
     * lorsqu'un formateur a déjà soumis une modification en attente de validation.
     */
    public function edit(AdminContext $context): KeyValueStore|Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            /** @var Formation|null $formation */
            $formation = $context->getEntity()->getInstance();

            if ($formation instanceof Formation) {
                $pending = $this->formationHistoryRepo->findPendingForFormation($formation);

                if ($pending !== null && $pending->getCreatedBy()?->getId() === $this->getUser()?->getId()) {
                    // Injecter les données de la révision dans l'entité en mémoire
                    // Le formulaire affichera les modifications en attente, pas les données live
                    $this->revisionService->applyRevisionDataToFormation(
                        $formation,
                        $this->revisionService->snapshotFromFormationHistory($pending)
                    );

                    if ($context->getRequest()->isMethod('GET')) {
                        $this->addFlash('info', 'Vous visualisez vos modifications en attente de validation. Vous pouvez les modifier jusqu\'à ce qu\'elles soient traitées.');
                    }
                }
            }
        }

        return parent::edit($context);
    }

    /**
     * Page d'historique des révisions pour une Formation donnée.
     * Affiche toutes les versions sous forme de timeline git-like avec diffs.
     */
    #[AdminRoute(path: '/{entityId}/historique', name: 'historique_formation')]
    public function historiqueFormation(
        AdminContext $context,
        FormationHistoryRepository $formationHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var Formation $formation */
        $formation  = $context->getEntity()->getInstance();
        $formationId = $formation->getId();

        $entries = $formationHistoryRepo->findHistoryForFormation($formation);

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

        $liveSnapshot = $this->revisionService->getLiveFormationSnapshot($formation);

        // Pré-calcul des snapshots pour chaque entrée
        $snapshots = [];
        foreach ($entries as $i => $entry) {
            $snapshots[$i] = $this->revisionService->snapshotFromFormationHistory($entry);
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

            if ($entry->getRevisionStatus() === FormationHistory::STATUS_PENDING) {
                $histEntry['approuverUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('approuverHistoriqueFormation')
                    ->setEntityId($formationId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId()
                    . '&returnUrl=' . urlencode($historiqueUrl);
                $histEntry['rejeterUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('rejeterHistoriqueFormation')
                    ->setEntityId($formationId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId()
                    . '&returnUrl=' . urlencode($historiqueUrl);
            }

            $restaurable = [FormationHistory::STATUS_APPROVED, FormationHistory::STATUS_AUTO_APPROVED];
            if (!$isCurrent && in_array($entry->getRevisionStatus(), $restaurable, true)) {
                $histEntry['restaurerUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('restaurerHistoriqueFormation')
                    ->setEntityId($formationId)
                    ->generateUrl()
                    . '?historyId=' . $entry->getId();
            }

            $historique[] = $histEntry;
        }

        return $this->render('admin/formation/historique.html.twig', [
            'formation'  => $formation,
            'historique' => $historique,
            'editUrl'    => $editUrl,
        ]);
    }

    /**
     * Approuve et applique une version de l'historique typé Formation.
     */
    #[AdminRoute(path: '/{entityId}/historique/approuver', name: 'approuver_historique_formation')]
    public function approuverHistoriqueFormation(
        AdminContext $context,
        FormationHistoryRepository $formationHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $formationHistoryRepo->find($historyId);

        if (!$history || $history->getRevisionStatus() !== FormationHistory::STATUS_PENDING) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->approuverFormationHistory($history, $reviewer);
        $this->revisionService->notifyAuthorFromHistory($history, true);

        $this->addFlash('success', sprintf('La révision de « %s » a été approuvée et appliquée.', $history->getTitle()));

        $returnUrl = $context->getRequest()->query->get('returnUrl');

        return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
    }

    /**
     * Rejette une version de l'historique typé Formation.
     */
    #[AdminRoute(path: '/{entityId}/historique/rejeter', name: 'rejeter_historique_formation')]
    public function rejeterHistoriqueFormation(
        AdminContext $context,
        FormationHistoryRepository $formationHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history   = $formationHistoryRepo->find($historyId);

        if (!$history || $history->getRevisionStatus() !== FormationHistory::STATUS_PENDING) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->rejeterFormationHistory($history, $reviewer);
        $this->revisionService->notifyAuthorFromHistory($history, false);

        $this->addFlash('info', sprintf('La révision de « %s » a été rejetée.', $history->getTitle()));

        $returnUrl = $context->getRequest()->query->get('returnUrl');

        return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
    }

    /**
     * Restaure une version de l'historique Formation sur l'entité live.
     */
    #[AdminRoute(path: '/{entityId}/historique/restaurer', name: 'restaurer_historique_formation')]
    public function restaurerHistoriqueFormation(
        AdminContext $context,
        FormationHistoryRepository $formationHistoryRepo,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $historyId   = (int) $context->getRequest()->query->get('historyId');
        $history     = $formationHistoryRepo->find($historyId);
        $formationId = $context->getEntity()->getInstance()?->getId();

        $returnUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('historiqueFormation')
            ->setEntityId($formationId)
            ->generateUrl();

        $restaurable = [FormationHistory::STATUS_APPROVED, FormationHistory::STATUS_AUTO_APPROVED];
        if (!$history || !in_array($history->getRevisionStatus(), $restaurable, true)) {
            $this->addFlash('danger', 'Version introuvable ou non restaurable.');

            return $this->redirect($returnUrl);
        }

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->restaurerFormationHistory($history, $reviewer);

        $this->addFlash('success', sprintf('La version v%d de « %s » a été restaurée.', $history->getVersion(), $history->getTitle()));

        return $this->redirect($returnUrl);
    }

    /**
     * Assigne automatiquement le créateur lors de la création d'une Formation.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Formation $entityInstance */
        if ($entityInstance->getCreatedBy() === null) {
            $entityInstance->setCreatedBy($this->getUser());
        }

        parent::persistEntity($entityManager, $entityInstance);
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
            // Vérifier s'il existe déjà une révision PENDING pour cette formation
            $existingPending = $this->formationHistoryRepo->findPendingForFormation($entityInstance);

            if ($existingPending !== null) {
                // Mettre à jour la révision PENDING existante (sans re-notifier les admins)
                $this->revisionService->updatePendingTypedHistory($entityInstance);
                $entityManager->refresh($entityInstance);
                $entityManager->flush();
                $this->addFlash('warning', 'Votre modification en attente a été mise à jour. Elle reste soumise à validation.');
            } else {
                // Première soumission : créer une nouvelle révision PENDING et notifier les admins
                $revision = $this->revisionService->createRevision($entityInstance, $user, false);
                $entityManager->refresh($entityInstance);
                $entityManager->flush();
                $this->revisionService->notifyAdmins($revision);
                $this->addFlash('warning', 'Votre modification a été soumise pour validation par un administrateur.');
            }

            return;
        }

        // Admin/Super-admin : créer une révision APPROVED et sauvegarder normalement
        $this->revisionService->createRevision($entityInstance, $user, true);
        parent::updateEntity($entityManager, $entityInstance);
    }
}
