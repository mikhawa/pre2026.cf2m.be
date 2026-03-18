<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Works;
use App\Service\RevisionService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Field\SunEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class WorksCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly RevisionService $revisionService,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Works::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, static fn (Action $a) => $a
                ->setLabel('Sauvegarder et continuer les changements')
                ->asWarningAction()
                ->setHtmlAttributes(['data-ea-btn' => 'continue'])
            )
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, static fn (Action $a) => $a
                ->asSuccessAction()
            )
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
