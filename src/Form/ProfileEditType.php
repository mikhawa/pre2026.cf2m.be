<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatarFile', VichImageType::class, [
                'label'              => 'Photo de profil',
                'required'           => false,
                'allow_delete'       => true,
                'delete_label'       => 'Supprimer la photo actuelle',
                'download_uri'       => false,
                'image_uri'          => false,
                'asset_helper'       => false,
                'attr'               => ['accept' => 'image/jpeg,image/png,image/webp'],
            ])
            ->add('biography', TextareaType::class, [
                'label'    => 'Présentation',
                'required' => false,
                'attr'     => [
                    'rows'        => 5,
                    'placeholder' => 'Quelques mots sur vous…',
                    'maxlength'   => 600,
                ],
            ])
            ->add('externalLink1', UrlType::class, [
                'label'    => 'Lien 1',
                'required' => false,
                'attr'     => ['placeholder' => 'https://linkedin.com/in/vous'],
                'default_protocol' => 'https',
            ])
            ->add('externalLink2', UrlType::class, [
                'label'    => 'Lien 2',
                'required' => false,
                'attr'     => ['placeholder' => 'https://github.com/vous'],
                'default_protocol' => 'https',
            ])
            ->add('externalLink3', UrlType::class, [
                'label'    => 'Lien 3',
                'required' => false,
                'attr'     => ['placeholder' => 'https://votre-site.be'],
                'default_protocol' => 'https',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
