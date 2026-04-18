<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Comment;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        // Un stagiaire ne voit que ses propres commentaires
        if (!$this->isGranted('ROLE_FORMATEUR')) {
            $qb->andWhere('entity.user = :currentUser')
               ->setParameter('currentUser', $this->getUser());
        }

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::DELETE)
            ->setPermission(Action::INDEX, 'PUBLIC_ACCESS')
            ->setPermission(Action::DETAIL, 'PUBLIC_ACCESS')
            ->setPermission(Action::EDIT, 'ROLE_FORMATEUR')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commentaire')
            ->setEntityLabelInPlural('Commentaires')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['content'])
            ->setPaginatorPageSize(25)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextareaField::new('content', 'Contenu');
        yield AssociationField::new('user', 'Utilisateur')
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT)
        ;
        yield AssociationField::new('works', 'Work')
            ->setFormTypeOption('disabled', $pageName === Crud::PAGE_EDIT)
        ;
        yield BooleanField::new('approved', 'Approuvé')
            ->renderAsSwitch(true)
        ;
        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('approved', 'Approuvé'))
            ->add(EntityFilter::new('user', 'Utilisateur'))
            ->add(EntityFilter::new('works', 'Work'))
        ;
    }
}
