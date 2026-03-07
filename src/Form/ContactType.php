<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'    => 'Votre nom',
                'attr'     => ['placeholder' => 'Jean Dupont', 'autocomplete' => 'name'],
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Votre adresse e-mail',
                'attr'     => ['placeholder' => 'jean@exemple.be', 'autocomplete' => 'email'],
            ])
            ->add('sujet', TextType::class, [
                'label'    => 'Sujet',
                'attr'     => ['placeholder' => 'Renseignements sur une formation'],
            ])
            ->add('message', TextareaType::class, [
                'label'    => 'Message',
                'attr'     => ['rows' => 6, 'placeholder' => 'Votre message…'],
            ])
            // Champ piège anti-robots (honeypot) — doit rester vide
            ->add('url', TextType::class, [
                'label'       => false,
                'mapped'      => false,
                'required'    => false,
                'attr'        => ['tabindex' => '-1', 'autocomplete' => 'off'],
                'constraints' => [new Blank(message: 'Ne pas remplir ce champ.')],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
        ]);
    }
}
