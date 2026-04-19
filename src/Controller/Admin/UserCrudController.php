<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
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

        if ($this->isGranted('ROLE_PEDAGO') && !$this->isGranted('ROLE_ADMIN')) {
            // ROLE_PEDAGO ne peut pas créer un utilisateur ROLE_ADMIN ou ROLE_SUPER_ADMIN
            $roles = array_values(array_filter(
                $entityInstance->getRoles(),
                static fn(string $r) => !in_array($r, ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], true)
            ));
            $entityInstance->setRoles($roles);
        } elseif (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            // ROLE_ADMIN ne peut pas créer un utilisateur ROLE_SUPER_ADMIN
            $roles = array_values(array_filter(
                $entityInstance->getRoles(),
                static fn(string $r) => $r !== 'ROLE_SUPER_ADMIN'
            ));
            $entityInstance->setRoles($roles);
        }

        if ($entityInstance->getStatus() === 0) {
            // Mot de passe placeholder : sera remplacé à l'activation
            $entityInstance->setPassword(bin2hex(random_bytes(32)));
            $token = bin2hex(random_bytes(32));
            $entityInstance->setActivationToken($token);

            parent::persistEntity($entityManager, $entityInstance);

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($this->mailFrom, 'CF2m Administration'))
                    ->to(new Address($entityInstance->getEmail()))
                    ->subject('Activez votre compte CF2m')
                    ->htmlTemplate('emails/user_activation_admin.html.twig')
                    ->context([
                        'user'  => $entityInstance,
                        'token' => $token,
                    ])
            );
        } else {
            $plainPassword = $this->generatePassword();
            $entityInstance->setPassword(
                $this->passwordHasher->hashPassword($entityInstance, $plainPassword)
            );

            parent::persistEntity($entityManager, $entityInstance);

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($this->mailFrom, 'CF2m Administration'))
                    ->to(new Address($entityInstance->getEmail()))
                    ->subject('Bienvenue sur CF2m — vos identifiants de connexion')
                    ->htmlTemplate('emails/user_bienvenue.html.twig')
                    ->context([
                        'user'          => $entityInstance,
                        'plainPassword' => $plainPassword,
                    ])
            );
        }
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
        // Un ROLE_ADMIN ne peut ni éditer ni consulter un ROLE_SUPER_ADMIN : on masque les boutons
        $isSuperAdmin = $this->isGranted('ROLE_SUPER_ADMIN');

        return $actions
            ->setPermission(Action::INDEX, 'CONTENT_MANAGER')
            ->setPermission(Action::NEW, 'CONTENT_MANAGER')
            ->setPermission(Action::EDIT, 'CONTENT_MANAGER')
            ->setPermission(Action::DETAIL, 'CONTENT_MANAGER')
            ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->displayIf(
                fn (User $user) => $isSuperAdmin || !in_array('ROLE_SUPER_ADMIN', $user->getRoles(), true)
            ))
        ;
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

    public function updateEntity(EntityManagerInterface $entityManager, mixed $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            if ($this->isGranted('ROLE_PEDAGO') && !$this->isGranted('ROLE_ADMIN')) {
                // ROLE_PEDAGO ne peut pas assigner ROLE_ADMIN ni ROLE_SUPER_ADMIN
                $roles = array_values(array_filter(
                    $entityInstance->getRoles(),
                    static fn(string $r) => !in_array($r, ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], true)
                ));
                $entityInstance->setRoles($roles);
            } elseif (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                // ROLE_ADMIN ne peut pas assigner ROLE_SUPER_ADMIN
                $roles = array_values(array_filter(
                    $entityInstance->getRoles(),
                    static fn(string $r) => $r !== 'ROLE_SUPER_ADMIN'
                ));
                $entityInstance->setRoles($roles);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('email', 'E-mail');
        yield TextField::new('userName', 'Nom d\'utilisateur');

        // En affichage (index) : tous les rôles sont visibles pour que les badges s'affichent correctement
        // En formulaire (new/edit) : ROLE_ADMIN ne peut attribuer que Formateur ou Administrateur
        $isFormPage = in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true);
        $rolesChoices = (!$isFormPage || $this->isGranted('ROLE_SUPER_ADMIN'))
            ? [
                'Utilisateur'    => 'ROLE_USER',
                'Stagiaire'      => 'ROLE_STAGIAIRE',
                'Formateur'      => 'ROLE_FORMATEUR',
                'Pédago'         => 'ROLE_PEDAGO',
                'Administrateur' => 'ROLE_ADMIN',
                'Super Admin'    => 'ROLE_SUPER_ADMIN',
            ]
            : ($isFormPage && $this->isGranted('ROLE_PEDAGO') && !$this->isGranted('ROLE_ADMIN')
                ? [
                    'Stagiaire'      => 'ROLE_STAGIAIRE',
                    'Formateur'      => 'ROLE_FORMATEUR',
                    'Pédago'         => 'ROLE_PEDAGO',
                ]
                : [
                    'Stagiaire'      => 'ROLE_STAGIAIRE',
                    'Formateur'      => 'ROLE_FORMATEUR',
                    'Pédago'         => 'ROLE_PEDAGO',
                    'Administrateur' => 'ROLE_ADMIN',
                ]
            );

        yield ChoiceField::new('roles', 'Rôles')
            ->setChoices($rolesChoices)
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_SUPER_ADMIN' => 'danger',
                'ROLE_ADMIN'       => 'warning',
                'ROLE_PEDAGO'      => 'primary',
                'ROLE_FORMATEUR'   => 'info',
                'ROLE_STAGIAIRE'   => 'success',
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
                'Stagiaire'      => 'ROLE_STAGIAIRE',
            ]))
        ;
    }
}
