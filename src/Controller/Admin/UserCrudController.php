<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MailerInterface $mailer,
        #[Autowire(env: 'MAIL_FORM')]
        private readonly string $mailFrom,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    /**
     * Génère un mot de passe temporaire de 12 caractères, le hache, l'envoie par email
     * et persiste le nouvel utilisateur.
     */
    public function persistEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            parent::persistEntity($entityManager, $entityInstance);

            return;
        }

        $plainPassword = $this->generatePassword();
        $entityInstance->setPassword(
            $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
        );

        parent::persistEntity($entityManager, $entityInstance);

        $email = (new TemplatedEmail())
            ->from(new Address($this->mailFrom, 'CF2m Administration'))
            ->to(new Address($entityInstance->getEmail()))
            ->subject('Bienvenue sur CF2m — vos identifiants de connexion')
            ->htmlTemplate('emails/user_bienvenue.html.twig')
            ->context([
                'user'          => $entityInstance,
                'plainPassword' => $plainPassword,
            ]);

        $this->mailer->send($email);
    }

    /** Génère un mot de passe aléatoire de 12 caractères (lettres + chiffres + symboles). */
    private function generatePassword(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%&*';
        $password = '';
        for ($i = 0; $i < 12; ++$i) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::INDEX, 'ROLE_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DETAIL, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
        ;
    }

    public function edit(AdminContext $context): KeyValueStore|Response
    {
        /** @var User|null $userToEdit */
        $userToEdit = $context->getEntity()->getInstance();

        if ($userToEdit !== null
            && in_array('ROLE_SUPER_ADMIN', $userToEdit->getRoles(), true)
            && !$this->isGranted('ROLE_SUPER_ADMIN')
        ) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier un Super Administrateur.');
        }

        return parent::edit($context);
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
            ->setPermission('ROLE_SUPER_ADMIN')
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
