<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Inscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'    => 'Nom',
                'attr'     => ['placeholder' => 'Votre nom'],
            ])
            ->add('prenom', TextType::class, [
                'label'    => 'Prénom',
                'attr'     => ['placeholder' => 'Votre prénom'],
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Adresse e-mail',
                'attr'     => ['placeholder' => 'votre@email.com'],
            ])
            ->add('telephone', TelType::class, [
                'label'    => 'Téléphone',
                'attr'     => ['placeholder' => '+32 4xx xx xx xx'],
            ])
            ->add('age', IntegerType::class, [
                'label'    => 'Âge',
                'attr'     => ['placeholder' => 'Votre âge', 'min' => 16, 'max' => 99],
            ])
            ->add('message', TextareaType::class, [
                'label'    => 'Message (optionnel)',
                'required' => false,
                'attr'     => ['placeholder' => 'Informations complémentaires…', 'rows' => 3],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
        ]);
    }
}
