<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Entity\FormationHistory;
use App\Entity\User;
use App\Field\SunEditorField;
use App\Repository\FormationHistoryRepository;
use App\Repository\FormationStagiaireRepository;
use App\Repository\UserRepository;
use App\Service\RevisionService;
use App\Service\StagiaireService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FormationCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RevisionService $revisionService,
        private readonly FormationHistoryRepository $formationHistoryRepo,
        private readonly StagiaireService $stagiaireService,
        private readonly FormationStagiaireRepository $formationStagiaireRepo,
        private readonly UserRepository $userRepository,
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
            ->setLabel(static function (Formation $entity) use ($formationHistoryRepo): TranslatableMessage {
                return new TranslatableMessage('Historique (%count%)', ['%count%' => count($formationHistoryRepo->findHistoryForFormation($entity))]);
            })
        ;

        $gererStagiaires = Action::new('gererStagiaires', 'Stagiaires', 'fa fa-user-graduate')
            ->linkToCrudAction('gererStagiaires')
        ;

        return $actions
            ->setPermission(Action::NEW, 'FORMATION_CREATE')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->setPermission('historiqueFormation', 'ROLE_FORMATEUR')
            ->setPermission('gererStagiaires', 'ROLE_FORMATEUR')
            ->add(Crud::PAGE_INDEX, $historique)
            ->add(Crud::PAGE_EDIT, $historique)
            ->add(Crud::PAGE_DETAIL, $historique)
            ->add(Crud::PAGE_INDEX, $gererStagiaires)
            ->add(Crud::PAGE_EDIT, $gererStagiaires)
            ->add(Crud::PAGE_DETAIL, $gererStagiaires)
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

    /**
     * Filtre la liste pour les formateurs : uniquement les formations dont ils sont responsables.
     * Les admins, super-admins et pédagos voient tout.
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_FORMATEUR') && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_PEDAGO')) {
            $qb->join('entity.responsables', 'r_formation')
                ->andWhere('r_formation.id = :currentUserId')
                ->setParameter('currentUserId', $this->getUser()?->getId());
        }

        return $qb;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $this->denyAccessUnlessGranted('ROLE_FORMATEUR');

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
        yield TextField::new('revisionPendante', false)
            ->onlyOnIndex()
            ->renderAsHtml()
            ->setSortable(false)
            ->formatValue(static function (mixed $value, ?Formation $entity) use ($formationHistoryRepo): string {
                if (null === $entity) {
                    return '';
                }
                $pending = $formationHistoryRepo->findPendingForFormation($entity);

                return null !== $pending
                    ? '<span class="badge text-bg-warning text-nowrap"><i class="fa fa-clock me-1"></i>En attente</span>'
                    : '';
            })
        ;
        yield TextField::new('title', 'Titre');
        yield TextField::new('slug', 'Slug');
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'Brouillon' => 'draft',
                'Publiée' => 'published',
                'Archivée' => 'archived',
                'Recrutement' => 'recruiting',
            ])
            ->renderAsBadges([
                'draft' => 'secondary',
                'published' => 'success',
                'archived' => 'danger',
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
            ->setQueryBuilder(fn (QueryBuilder $qb) => $qb
                ->andWhere('entity.roles LIKE :roleFormateur')
                ->setParameter('roleFormateur', '%ROLE_FORMATEUR%')
            )
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
                'Brouillon' => 'draft',
                'Publiée' => 'published',
                'Archivée' => 'archived',
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
        /** @var Formation|null $formation */
        $formation = $context->getEntity()->getInstance();

        // Un formateur non-admin ne peut éditer que les formations dont il est responsable
        if ($this->isGranted('ROLE_FORMATEUR') && !$this->isGranted('ROLE_ADMIN')) {
            if ($formation instanceof Formation && !$this->isGranted('FORMATION_APPROVE', $formation)) {
                throw $this->createAccessDeniedException('Vous n\'êtes pas responsable de cette formation.');
            }
        }

        if (!$this->isGranted('ROLE_FORMATEUR')) {
            /** @var Formation|null $formation */
            $formation = $context->getEntity()->getInstance();

            if ($formation instanceof Formation) {
                $pending = $this->formationHistoryRepo->findPendingForFormation($formation);

                if (null !== $pending && $pending->getCreatedBy()?->getId() === $this->getUser()?->getId()) {
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
        $this->denyAccessUnlessGranted('ROLE_FORMATEUR');

        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
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
        $currentFound = false;
        foreach ($entries as $i => $entry) {
            $isCurrent = !$currentFound && ($snapshots[$i] === $liveSnapshot);
            if ($isCurrent) {
                $currentFound = true;
            }

            $diff = $this->revisionService->buildTypedHistoryDiffHtml(
                $snapshots[$i],
                $snapshots[$i + 1] ?? null
            );

            $histEntry = [
                'revision' => $entry,
                'diff' => $diff,
                'isCurrent' => $isCurrent,
            ];

            if (FormationHistory::STATUS_PENDING === $entry->getRevisionStatus()) {
                $histEntry['approuverUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('approuverHistoriqueFormation')
                    ->setEntityId($formationId)
                    ->generateUrl()
                    .'?historyId='.$entry->getId()
                    .'&returnUrl='.urlencode($historiqueUrl);
                $histEntry['rejeterUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('rejeterHistoriqueFormation')
                    ->setEntityId($formationId)
                    ->generateUrl()
                    .'?historyId='.$entry->getId()
                    .'&returnUrl='.urlencode($historiqueUrl);
            }

            $restaurable = [FormationHistory::STATUS_APPROVED, FormationHistory::STATUS_AUTO_APPROVED];
            if (!$isCurrent && in_array($entry->getRevisionStatus(), $restaurable, true)) {
                $histEntry['restaurerUrl'] = $adminUrlGenerator
                    ->setController(self::class)
                    ->setAction('restaurerHistoriqueFormation')
                    ->setEntityId($formationId)
                    ->generateUrl()
                    .'?historyId='.$entry->getId();
            }

            $historique[] = $histEntry;
        }

        return $this->render('admin/formation/historique.html.twig', [
            'formation' => $formation,
            'historique' => $historique,
            'editUrl' => $editUrl,
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
        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('FORMATION_APPROVE', $formation);

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history = $formationHistoryRepo->find($historyId);

        if (!$history || FormationHistory::STATUS_PENDING !== $history->getRevisionStatus()) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var User $reviewer */
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
        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('FORMATION_REJECT', $formation);

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history = $formationHistoryRepo->find($historyId);

        if (!$history || FormationHistory::STATUS_PENDING !== $history->getRevisionStatus()) {
            $this->addFlash('danger', 'Révision introuvable ou déjà traitée.');
            $returnUrl = $context->getRequest()->query->get('returnUrl');

            return $returnUrl ? $this->redirect($returnUrl) : $this->redirectToRoute('admin');
        }

        /** @var User $reviewer */
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
        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('FORMATION_RESTORE', $formation);

        $historyId = (int) $context->getRequest()->query->get('historyId');
        $history = $formationHistoryRepo->find($historyId);
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

        /** @var User $reviewer */
        $reviewer = $this->getUser();
        $this->revisionService->restaurerFormationHistory($history, $reviewer);

        $this->addFlash('success', sprintf('La version v%d de « %s » a été restaurée.', $history->getVersion(), $history->getTitle()));

        return $this->redirect($returnUrl);
    }

    /**
     * Page de gestion des stagiaires d'une Formation : liste des stagiaires actuels
     * et formulaire d'ajout d'un nouvel utilisateur.
     */
    #[AdminRoute(path: '/{entityId}/stagiaires', name: 'gerer_stagiaires_formation')]
    public function gererStagiaires(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('FORMATION_MANAGE_STAGIAIRES', $formation);

        $formationId = $formation->getId();

        $stagiaires = $this->formationStagiaireRepo->findForFormation($formation);

        // Utilisateurs candidats : tous ceux qui ne sont pas déjà stagiaires de cette formation.
        // Un formateur/admin peut aussi être ajouté comme stagiaire (décision du doc de proposition).
        // Amélioration UX future possible : remplacer le <select> par un champ autocomplete Ajax.
        $dejaStagiaireIds = array_map(
            static fn ($fs): ?int => $fs->getUser()?->getId(),
            $stagiaires
        );
        $candidats = array_filter(
            $this->userRepository->findAllOrderedByName(),
            static fn (User $u): bool => !in_array($u->getId(), $dejaStagiaireIds, true)
        );

        $editUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($formationId)
            ->generateUrl();

        $ajouterUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('ajouterStagiaireFormation')
            ->setEntityId($formationId)
            ->generateUrl();

        $retirerUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('retirerStagiaireFormation')
            ->setEntityId($formationId)
            ->generateUrl();

        return $this->render('admin/formation/stagiaires.html.twig', [
            'formation' => $formation,
            'stagiaires' => $stagiaires,
            'candidats' => $candidats,
            'editUrl' => $editUrl,
            'ajouterUrl' => $ajouterUrl,
            'retirerUrl' => $retirerUrl,
        ]);
    }

    /**
     * Traite l'ajout d'un stagiaire à une Formation (POST depuis la page de gestion).
     */
    #[AdminRoute(path: '/{entityId}/stagiaires/ajouter', name: 'ajouter_stagiaire_formation')]
    public function ajouterStagiaireFormation(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('FORMATION_MANAGE_STAGIAIRES', $formation);

        $retourUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('gererStagiaires')
            ->setEntityId($formation->getId())
            ->generateUrl();

        $userId = (int) $context->getRequest()->request->get('userId');
        $user = $userId > 0 ? $this->userRepository->find($userId) : null;

        if (!$user instanceof User) {
            $this->addFlash('danger', 'Utilisateur introuvable.');

            return $this->redirect($retourUrl);
        }

        /** @var User $addedBy */
        $addedBy = $this->getUser();
        $this->stagiaireService->ajouterStagiaire($formation, $user, $addedBy);

        $this->addFlash('success', sprintf('%s a été ajouté comme stagiaire de cette formation.', $user->getUserName()));

        return $this->redirect($retourUrl);
    }

    /**
     * Traite le retrait d'un stagiaire d'une Formation (lien avec confirmation JS).
     */
    #[AdminRoute(path: '/{entityId}/stagiaires/retirer', name: 'retirer_stagiaire_formation')]
    public function retirerStagiaireFormation(
        AdminContext $context,
        AdminUrlGenerator $adminUrlGenerator,
    ): Response {
        /** @var Formation $formation */
        $formation = $context->getEntity()->getInstance();
        $this->denyAccessUnlessGranted('FORMATION_MANAGE_STAGIAIRES', $formation);

        $retourUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction('gererStagiaires')
            ->setEntityId($formation->getId())
            ->generateUrl();

        $userId = (int) $context->getRequest()->query->get('userId');
        $user = $userId > 0 ? $this->userRepository->find($userId) : null;

        if (!$user instanceof User) {
            $this->addFlash('danger', 'Utilisateur introuvable.');

            return $this->redirect($retourUrl);
        }

        $etaitDerniere = $this->stagiaireService->retirerStagiaire($formation, $user);

        if ($etaitDerniere) {
            $this->addFlash('warning', sprintf(
                '%s a été retiré de cette formation. Il s\'agissait de sa dernière formation : il a perdu l\'accès à l\'espace admin (ROLE_STAGIAIRE).',
                $user->getUserName()
            ));
        } else {
            $this->addFlash('success', sprintf('%s a été retiré de cette formation.', $user->getUserName()));
        }

        return $this->redirect($retourUrl);
    }

    /**
     * Assigne automatiquement le créateur lors de la création d'une Formation.
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Formation $entityInstance */
        if (null === $entityInstance->getCreatedBy()) {
            $entityInstance->setCreatedBy($this->getUser());
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    /**
     * Intercepte la mise à jour pour gérer les révisions.
     * - ROLE_FORMATEUR (sans ROLE_ADMIN) : révision PENDING, contenu live inchangé
     * - ROLE_ADMIN / ROLE_SUPER_ADMIN : révision AUTO_APPROVED, contenu live mis à jour.
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var Formation $entityInstance */
        $user = $this->getUser();

        if (!$this->isGranted('FORMATION_EDIT_AUTOAPPROVE', $entityInstance)) {
            // Vérifier s'il existe déjà une révision PENDING pour cette formation
            $existingPending = $this->formationHistoryRepo->findPendingForFormation($entityInstance);

            if (null !== $existingPending) {
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
                if (null === $revision) {
                    $this->addFlash('info', 'Aucune modification détectée, la formation n\'a pas été mise à jour.');

                    return;
                }
                $this->revisionService->notifyAdmins($revision);
                $this->addFlash('warning', 'Votre modification a été soumise pour validation par un administrateur.');
            }

            return;
        }

        // Admin/Super-admin : créer une révision APPROVED et sauvegarder normalement
        /* @var \App\Entity\User $user */
        $entityInstance->setUpdatedBy($user);
        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        $this->revisionService->createRevision($entityInstance, $user, true);
        parent::updateEntity($entityManager, $entityInstance);
    }
}
