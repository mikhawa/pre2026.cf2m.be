<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Service\RevisionService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

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
        return $actions
            ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
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
        yield TextEditorField::new('description', 'Description')
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
