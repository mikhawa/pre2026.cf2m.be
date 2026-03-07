<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['email', 'userName'])
            ->setPaginatorPageSize(25)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('email', 'E-mail');
        yield TextField::new('userName', 'Nom d\'utilisateur');
        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices([
                'Utilisateur'     => 'ROLE_USER',
                'Administrateur'  => 'ROLE_ADMIN',
                'Super Admin'     => 'ROLE_SUPER_ADMIN',
                'Formateur'       => 'ROLE_FORMATEUR',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_SUPER_ADMIN' => 'danger',
                'ROLE_ADMIN'       => 'warning',
                'ROLE_FORMATEUR'   => 'info',
                'ROLE_USER'        => 'secondary',
            ])
        ;
        yield IntegerField::new('status', 'Statut')
            ->hideOnIndex()
        ;
        yield DateTimeField::new('createdAt', 'Créé le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm')
        ;
        yield TextareaField::new('biography', 'Biographie')
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield TextField::new('externalLink1', 'Lien externe 1')
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield TextField::new('externalLink2', 'Lien externe 2')
            ->hideOnIndex()
            ->setRequired(false)
        ;
        yield TextField::new('externalLink3', 'Lien externe 3')
            ->hideOnIndex()
            ->setRequired(false)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('roles')->setChoices([
                'Super Admin'    => 'ROLE_SUPER_ADMIN',
                'Administrateur' => 'ROLE_ADMIN',
                'Formateur'      => 'ROLE_FORMATEUR',
            ]))
        ;
    }
}
