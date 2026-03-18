<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Revision;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

class RevisionCrudController extends AbstractCrudController
{
    public function __construct(
        protected readonly RevisionService $revisionService,
        protected readonly EntityManagerInterface $em,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Revision::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Révision')
            ->setEntityLabelInPlural('Révisions')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['entityTitle', 'entityType'])
            ->setPaginatorPageSize(20)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Action « Approuver » — visible uniquement si PENDING
        $approuver = Action::new('approuverRevision', 'Approuver', 'fa fa-check')
            ->linkToCrudAction('approuverRevision')
            ->setCssClass('btn btn-success')
            ->displayIf(static fn (Revision $revision): bool => $revision->getStatus() === Revision::STATUS_PENDING)
        ;

        // Action « Rejeter » — visible uniquement si PENDING
        $rejeter = Action::new('rejeterRevision', 'Rejeter', 'fa fa-times')
            ->linkToCrudAction('rejeterRevision')
            ->setCssClass('btn btn-danger')
            ->displayIf(static fn (Revision $revision): bool => $revision->getStatus() === Revision::STATUS_PENDING)
        ;

        // Action « Restaurer » — visible uniquement si APPROVED et sauvegarde disponible
        $restaurer = Action::new('restaurerRevision', 'Restaurer', 'fa fa-undo')
            ->linkToCrudAction('restaurerRevision')
            ->setCssClass('btn btn-info')
            ->displayIf(static fn (Revision $revision): bool => $revision->getStatus() === Revision::STATUS_APPROVED && $revision->getPreviousData() !== null)
        ;

        return $actions
            // Désactiver les actions de création, édition et suppression
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
            // Permissions : INDEX et DETAIL réservés aux ROLE_ADMIN
            ->setPermission(Action::INDEX, 'ROLE_ADMIN')
            ->setPermission(Action::DETAIL, 'ROLE_ADMIN')
            // Lien "Voir" sur la liste → page détail (EDIT étant désactivé)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // Actions custom sur la page DETAIL
            ->add(Crud::PAGE_DETAIL, $approuver)
            ->add(Crud::PAGE_DETAIL, $rejeter)
            ->add(Crud::PAGE_DETAIL, $restaurer)
            // Actions custom sur la liste (accès rapide)
            ->add(Crud::PAGE_INDEX, $approuver)
            ->add(Crud::PAGE_INDEX, $rejeter)
            ->add(Crud::PAGE_INDEX, $restaurer)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('entityType', 'Type')
            ->setChoices([
                'Formation' => 'formation',
                'Page'      => 'page',
                'Works'     => 'works',
            ])
            ->renderAsBadges([
                'formation' => 'primary',
                'page'      => 'info',
                'works'     => 'secondary',
            ])
        ;
        yield TextField::new('entityTitle', 'Titre de l\'entité');
        yield AssociationField::new('createdBy', 'Auteur');
        yield DateTimeField::new('createdAt', 'Créée le')
            ->setFormat('dd/MM/yyyy HH:mm')
        ;
        yield ChoiceField::new('status', 'Statut')
            ->setChoices([
                'En attente' => Revision::STATUS_PENDING,
                'Approuvée'  => Revision::STATUS_APPROVED,
                'Rejetée'    => Revision::STATUS_REJECTED,
            ])
            ->renderAsBadges([
                Revision::STATUS_PENDING  => 'warning',
                Revision::STATUS_APPROVED => 'success',
                Revision::STATUS_REJECTED => 'danger',
            ])
        ;
        yield AssociationField::new('reviewedBy', 'Validée par')
            ->hideOnIndex()
        ;
        yield DateTimeField::new('reviewedAt', 'Validée le')
            ->setFormat('dd/MM/yyyy HH:mm')
            ->hideOnIndex()
        ;

        // Tableau de comparaison : valeur actuelle vs valeur proposée (page détail uniquement)
        // Utilise getDiffDisplay() (string vide) comme accroche car EasyAdmin rejette array sur TextField
        $revisionService = $this->revisionService;
        yield TextField::new('diffDisplay', 'Comparaison des modifications')
            ->hideOnIndex()
            ->hideOnForm()
            ->renderAsHtml()
            ->formatValue(static function (mixed $value, Revision $revision) use ($revisionService): string {
                return $revisionService->buildDiffHtml($revision);
            })
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                'En attente' => Revision::STATUS_PENDING,
                'Approuvée'  => Revision::STATUS_APPROVED,
                'Rejetée'    => Revision::STATUS_REJECTED,
            ]))
            ->add(ChoiceFilter::new('entityType')->setChoices([
                'Formation' => 'formation',
                'Page'      => 'page',
                'Works'     => 'works',
            ]))
        ;
    }

    /**
     * Approuve une révision PENDING : applique le snapshot au contenu live.
     */
    #[AdminRoute(path: '/{entityId}/approuver', name: 'approuver_revision')]
    public function approuverRevision(AdminContext $context): Response
    {
        /** @var Revision $revision */
        $revision = $context->getEntity()->getInstance();

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();
        $revision->setStatus(Revision::STATUS_APPROVED);
        $revision->setReviewedBy($reviewer);
        $revision->setReviewedAt(new \DateTimeImmutable());

        $this->revisionService->applyRevision($revision, $reviewer);
        $this->revisionService->notifyAuthor($revision, true);

        $this->addFlash('success', sprintf('La révision « %s » a été approuvée et appliquée.', $revision->getEntityTitle()));

        return $this->redirectToIndex($context);
    }

    /**
     * Rejette une révision PENDING : le contenu live n'est pas modifié.
     */
    #[AdminRoute(path: '/{entityId}/rejeter', name: 'rejeter_revision')]
    public function rejeterRevision(AdminContext $context): Response
    {
        /** @var Revision $revision */
        $revision = $context->getEntity()->getInstance();

        $revision->setStatus(Revision::STATUS_REJECTED);
        $revision->setReviewedBy($this->getUser());
        $revision->setReviewedAt(new \DateTimeImmutable());

        $this->em->flush();
        $this->revisionService->notifyAuthor($revision, false);

        $this->addFlash('warning', sprintf('La révision « %s » a été rejetée.', $revision->getEntityTitle()));

        return $this->redirectToIndex($context);
    }

    /**
     * Restaure l'état précédent stocké dans previousData de la révision.
     * Permute avec l'état courant (undo/redo possible).
     */
    #[AdminRoute(path: '/{entityId}/restaurer', name: 'restaurer_revision')]
    public function restaurerRevision(AdminContext $context): Response
    {
        /** @var Revision $revision */
        $revision = $context->getEntity()->getInstance();

        try {
            $this->revisionService->applyPreviousData($revision);
            $this->addFlash('success', sprintf('La révision « %s » a été restaurée (état précédent appliqué).', $revision->getEntityTitle()));
        } catch (\RuntimeException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToIndex($context);
    }

    /**
     * Applique les données d'une révision au contenu live (restauration historique).
     * Sauvegarde l'état courant en base avant d'appliquer la version cible.
     */
    #[AdminRoute(path: '/{entityId}/appliquer-version', name: 'appliquer_version')]
    public function appliquerVersion(AdminContext $context): Response
    {
        /** @var Revision $revision */
        $revision = $context->getEntity()->getInstance();

        /** @var \App\Entity\User $reviewer */
        $reviewer = $this->getUser();

        try {
            $this->revisionService->appliquerVersion($revision, $reviewer);
            $this->addFlash(
                'success',
                sprintf(
                    'La version du %s a été appliquée au contenu live.',
                    $revision->getCreatedAt()?->format('d/m/Y à H\hi')
                )
            );
        } catch (\Throwable $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToIndex($context);
    }

    /**
     * Redirige vers la liste des révisions ou vers l'URL de retour si fournie.
     * Permet aux pages d'historique de définir un returnUrl pour revenir après action.
     */
    protected function redirectToIndex(AdminContext $context): Response
    {
        $returnUrl = $context->getRequest()->query->get('returnUrl');
        if ($returnUrl) {
            return $this->redirect($returnUrl);
        }

        /** @var AdminUrlGenerator $adminUrlGenerator */
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl()
        ;

        return $this->redirect($url);
    }
}
