<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ContactMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactMessage::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message de contact')
            ->setEntityLabelInPlural('Messages de contact')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['nom', 'email', 'sujet'])
            ->setPaginatorPageSize(25)
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom');
        yield EmailField::new('email', 'E-mail');
        yield TextField::new('sujet', 'Sujet');
        yield DateTimeField::new('createdAt', 'Reçu le')
            ->hideOnForm()
            ->setFormat('dd/MM/yyyy HH:mm')
        ;
        yield BooleanField::new('read', 'Lu')
            ->renderAsSwitch(true)
        ;
        yield AssociationField::new('readBy', 'Lu par')
            ->hideOnForm()
            ->setRequired(false)
        ;
        yield TextareaField::new('message', 'Message')
            ->hideOnIndex()
            ->setRequired(false)
        ;
    }

    /**
     * Lors de la sauvegarde : si le message est marqué comme lu, on enregistre
     * l'utilisateur connecté comme lecteur. Si décoché, on efface le lecteur.
     */
    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if ($entityInstance instanceof ContactMessage) {
            /** @var User|null $currentUser */
            $currentUser = $this->getUser();

            if ($entityInstance->isRead()) {
                $entityInstance->setReadBy($currentUser);
            } else {
                $entityInstance->setReadBy(null);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
