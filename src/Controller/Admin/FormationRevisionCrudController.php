<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Revision;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;

/**
 * Historique des révisions filtrées sur les Formations.
 */
class FormationRevisionCrudController extends RevisionCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Révision de formation')
            ->setEntityLabelInPlural('Historique des formations')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $appliquer = Action::new('appliquerVersion', 'Appliquer cette version', 'fa fa-code-branch')
            ->linkToCrudAction('appliquerVersion')
            ->setCssClass('btn btn-warning')
            ->displayIf(static fn (Revision $r): bool => $r->getStatus() === Revision::STATUS_APPROVED)
        ;

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $appliquer)
            ->add(Crud::PAGE_DETAIL, $appliquer)
            ->setPermission('appliquerVersion', 'ROLE_ADMIN')
        ;
    }

    public function createIndexQueryBuilder(
        SearchDto $searchDto,
        EntityDto $entityDto,
        FieldCollection $fields,
        FilterCollection $filters,
    ): QueryBuilder {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.entityType = :revType')
            ->setParameter('revType', 'formation')
        ;
    }
}
